<?php
namespace App\Services;
use App\Core\Database;
use App\Models\Santri;
use App\Models\Peminjaman;
use App\Models\Setting;
use Exception;
class SirkulasiService {
    public function pinjamBuku(int $santriId, array $bukuIds, ?string $catatan, int $petugasId): array {
        if (empty($bukuIds)) throw new Exception("Pilih minimal satu buku.");
        $bukuIds = array_values(array_unique(array_filter(array_map('intval', $bukuIds))));
        $santriModel = new Santri();
        $settingModel = new Setting();
        $peminjamanModel = new Peminjaman();
        $santri = $santriModel->find($santriId);
        if (!$santri) throw new Exception("Data santri tidak ditemukan.");
        if ($santri['status'] !== 'aktif') throw new Exception("Santri tidak berstatus aktif.");
        $maksimalPinjaman = (int) $settingModel->get('maksimal_pinjaman', 3);
        $lamaPeminjaman = (int) $settingModel->get('lama_peminjaman', 7);
        $pinjamanAktif = $santriModel->getActiveLoansCount($santriId);
        if (($pinjamanAktif + count($bukuIds)) > $maksimalPinjaman) {
            throw new Exception("Peminjaman melebihi batas maksimal ($maksimalPinjaman buku).");
        }
        return Database::transaction(function (\PDO $db) use ($santri, $santriId, $bukuIds, $catatan, $petugasId, $lamaPeminjaman, $peminjamanModel) {
            $today = date('Y-m-d');
            $jatuhTempo = date('Y-m-d', strtotime("+{$lamaPeminjaman} days"));
            $kodeTrx = $peminjamanModel->generateKodeTransaksi();
            $selectedBooks = [];
            foreach ($bukuIds as $bId) {
                $stmt = $db->prepare("SELECT id, kode_buku, judul, jumlah_tersedia, status FROM `buku` WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $bId]);
                $b = $stmt->fetch();
                if (!$b || $b['status'] !== 'aktif' || $b['jumlah_tersedia'] < 1) {
                    throw new Exception("Stok buku " . ($b['judul'] ?? "#$bId") . " tidak tersedia.");
                }
                $selectedBooks[] = $b;
            }
            $stmtHeader = $db->prepare("INSERT INTO `peminjaman` (`kode_transaksi`, `santri_id`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `status`, `denda`, `catatan`, `petugas_id`, `created_at`) VALUES (:k, :s, :tp, :jt, 'dipinjam', 0.00, :c, :p, NOW())");
            $stmtHeader->execute(['k' => $kodeTrx, 's' => $santriId, 'tp' => $today, 'jt' => $jatuhTempo, 'c' => $catatan ?: null, 'p' => $petugasId]);
            $peminjamanId = (int) $db->lastInsertId();
            $stmtDetail = $db->prepare("INSERT INTO `peminjaman_detail` (`peminjaman_id`, `buku_id`, `kondisi_pinjam`, `denda`, `status`) VALUES (:pid, :bid, 'baik', 0.00, 'dipinjam')");
            $stmtStok = $db->prepare("UPDATE `buku` SET `jumlah_tersedia` = `jumlah_tersedia` - 1, `jumlah_dipinjam` = `jumlah_dipinjam` + 1 WHERE id = :id");
            foreach ($selectedBooks as $b) {
                $stmtDetail->execute(['pid' => $peminjamanId, 'bid' => $b['id']]);
                $stmtStok->execute(['id' => $b['id']]);
            }
            LogService::record('PEMINJAMAN', 'TRANSAKSI', "Peminjaman {$kodeTrx} untuk santri {$santri['nama']} (" . count($selectedBooks) . " buku)", $petugasId);
            return ['peminjaman_id' => $peminjamanId, 'kode_transaksi' => $kodeTrx, 'tanggal_pinjam' => $today, 'tanggal_jatuh_tempo' => $jatuhTempo, 'total_buku' => count($selectedBooks)];
        });
    }
    public function prosesPengembalian(int $peminjamanId, array $itemsData, int $petugasId): array {
        $peminjamanModel = new Peminjaman();
        $trx = $peminjamanModel->findWithFullDetails($peminjamanId);
        if (!$trx) throw new Exception("Transaksi tidak ditemukan.");
        $settingModel = new Setting();
        $dendaPerHari = (float) $settingModel->get('denda_per_hari', 1000);
        $dendaRusakRingan = (float) $settingModel->get('denda_rusak_ringan', 5000);
        $dendaRusakBerat = (float) $settingModel->get('denda_rusak_berat', 20000);
        return Database::transaction(function (\PDO $db) use ($trx, $peminjamanId, $itemsData, $petugasId, $dendaPerHari, $dendaRusakRingan, $dendaRusakBerat) {
            $today = date('Y-m-d');
            $hariTerlambat = max(0, (int)floor((strtotime($today) - strtotime($trx['tanggal_jatuh_tempo'])) / 86400));
            $stmtSel = $db->prepare("SELECT * FROM `peminjaman_detail` WHERE id = :id AND peminjaman_id = :pid FOR UPDATE");
            $stmtUpd = $db->prepare("UPDATE `peminjaman_detail` SET `tanggal_kembali` = :tk, `kondisi_kembali` = :k, `denda` = :d, `status` = :st, `catatan` = :c WHERE id = :id");
            $processed = 0;
            foreach ($itemsData as $item) {
                $detailId = (int)($item['detail_id'] ?? 0);
                if ($detailId <= 0) continue;
                $stmtSel->execute(['id' => $detailId, 'pid' => $peminjamanId]);
                $detail = $stmtSel->fetch();
                if (!$detail || $detail['status'] !== 'dipinjam') continue;
                $kondisi = in_array($item['kondisi_kembali'] ?? '', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'], true) ? $item['kondisi_kembali'] : 'baik';
                $dendaItem = $hariTerlambat * $dendaPerHari;
                if ($kondisi === 'rusak_ringan') $dendaItem += $dendaRusakRingan;
                elseif ($kondisi === 'rusak_berat') $dendaItem += $dendaRusakBerat;
                elseif ($kondisi === 'hilang') $dendaItem += ($dendaRusakBerat * 2);
                $statusItem = ($kondisi === 'hilang') ? 'hilang' : 'kembali';
                $stmtUpd->execute(['tk' => $today, 'k' => $kondisi, 'd' => $dendaItem, 'st' => $statusItem, 'c' => trim((string)($item['catatan'] ?? '')) ?: null, 'id' => $detailId]);
                $bId = (int)$detail['buku_id'];
                if ($kondisi === 'baik' || $kondisi === 'rusak_ringan') {
                    $db->prepare("UPDATE `buku` SET `jumlah_dipinjam` = `jumlah_dipinjam` - 1, `jumlah_tersedia` = `jumlah_tersedia` + 1 WHERE id = :id")->execute(['id' => $bId]);
                } elseif ($kondisi === 'rusak_berat') {
                    $db->prepare("UPDATE `buku` SET `jumlah_dipinjam` = `jumlah_dipinjam` - 1, `jumlah_rusak` = `jumlah_rusak` + 1 WHERE id = :id")->execute(['id' => $bId]);
                } elseif ($kondisi === 'hilang') {
                    $db->prepare("UPDATE `buku` SET `jumlah_dipinjam` = `jumlah_dipinjam` - 1, `jumlah_hilang` = `jumlah_hilang` + 1 WHERE id = :id")->execute(['id' => $bId]);
                }
                $processed++;
            }
            if ($processed === 0) throw new Exception("Tidak ada buku yang diproses.");
            $sisa = (int)$db->prepare("SELECT COUNT(*) as s FROM `peminjaman_detail` WHERE peminjaman_id = :id AND status = 'dipinjam'")->execute(['id' => $peminjamanId]) ? $db->query("SELECT COUNT(*) FROM `peminjaman_detail` WHERE peminjaman_id = {$peminjamanId} AND status = 'dipinjam'")->fetchColumn() : 0;
            $statusBaru = ($sisa == 0) ? 'selesai' : 'sebagian_dikembalikan';
            $stmtTD = $db->prepare("SELECT SUM(denda) as td FROM `peminjaman_detail` WHERE peminjaman_id = :id");
            $stmtTD->execute(['id' => $peminjamanId]);
            $totalDenda = (float)($stmtTD->fetch()['td'] ?? 0.0);
            $db->prepare("UPDATE `peminjaman` SET `status` = :st, `tanggal_kembali` = CASE WHEN :sisa = 0 THEN :tk ELSE `tanggal_kembali` END, `denda` = :td, `updated_at` = NOW() WHERE id = :id")
               ->execute(['st' => $statusBaru, 'sisa' => $sisa, 'tk' => $today, 'td' => $totalDenda, 'id' => $peminjamanId]);
            LogService::record('PENGEMBALIAN', 'TRANSAKSI', "Pengembalian {$processed} buku transaksi {$trx['kode_transaksi']}", $petugasId);
            return ['kode_transaksi' => $trx['kode_transaksi'], 'items_processed' => $processed, 'sisa_pinjam' => (int)$sisa, 'status_transaksi' => $statusBaru, 'denda_transaksi' => $totalDenda];
        });
    }
}
