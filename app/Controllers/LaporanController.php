<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Kategori;

class LaporanController extends Controller {
    public function index(): void { $this->view('laporan.index', ['pageTitle' => 'Pusat Laporan & Rekapitulasi']); }
    public function buku(Request $request): void {
        $db = Database::getConnection();
        $status = trim((string)$request->get('status', ''));
        $kategoriId = (int)$request->get('kategori', 0);
        $format = trim((string)$request->get('format', 'html'));
        $where = ["1=1"]; $params = [];
        if ($status === 'tersedia') $where[] = "b.jumlah_tersedia > 0";
        elseif ($status === 'dipinjam') $where[] = "b.jumlah_dipinjam > 0";
        elseif ($status === 'rusak') $where[] = "b.jumlah_rusak > 0";
        elseif ($status === 'hilang') $where[] = "b.jumlah_hilang > 0";
        if ($kategoriId > 0) { $where[] = "b.kategori_id = :kat"; $params['kat'] = $kategoriId; }
        $sql = "SELECT b.*, k.nama as nama_kategori, r.kode_rak, r.nama_rak FROM `buku` b LEFT JOIN `kategori` k ON b.kategori_id=k.id LEFT JOIN `rak` r ON b.rak_id=r.id WHERE " . implode(' AND ', $where) . " ORDER BY b.judul ASC";
        $stmt = $db->prepare($sql); $stmt->execute($params); $books = $stmt->fetchAll();
        if ($format === 'csv') {
            $this->exportCsv('laporan_buku_' . date('Ymd_His') . '.csv', ['Kode', 'ISBN', 'Judul', 'Penulis', 'Penerbit', 'Tahun', 'Kategori', 'Rak', 'Total', 'Tersedia', 'Dipinjam', 'Rusak', 'Hilang', 'Status'], array_map(fn($b) => [$b['kode_buku'], $b['isbn'], $b['judul'], $b['penulis'], $b['penerbit'], $b['tahun_terbit'], $b['nama_kategori'], $b['kode_rak'], $b['jumlah_total'], $b['jumlah_tersedia'], $b['jumlah_dipinjam'], $b['jumlah_rusak'], $b['jumlah_hilang'], $b['status']], $books));
            return;
        }
        $this->view('laporan.buku', ['pageTitle' => 'Laporan Inventaris Buku', 'books' => $books, 'kategoriList' => (new Kategori())->allActive(), 'status' => $status, 'kategoriId' => $kategoriId, 'isPrint' => ($format === 'print')], ($format === 'print' ? false : 'main'));
    }
    public function santri(Request $request): void {
        $db = Database::getConnection();
        $filter = trim((string)$request->get('filter', ''));
        $kelas = trim((string)$request->get('kelas', ''));
        $format = trim((string)$request->get('format', 'html'));
        $where = ["1=1"]; $params = [];
        if ($kelas !== '') { $where[] = "s.kelas = :kelas"; $params['kelas'] = $kelas; }
        if ($filter === 'meminjam') $where[] = "s.id IN (SELECT DISTINCT p.santri_id FROM peminjaman p JOIN peminjaman_detail pd ON p.id=pd.peminjaman_id WHERE pd.status='dipinjam')";
        elseif ($filter === 'terlambat') $where[] = "s.id IN (SELECT DISTINCT p.santri_id FROM peminjaman p JOIN peminjaman_detail pd ON p.id=pd.peminjaman_id WHERE pd.status='dipinjam' AND p.tanggal_jatuh_tempo < CURRENT_DATE)";
        $sql = "SELECT s.*, (SELECT COUNT(pd.id) FROM peminjaman_detail pd JOIN peminjaman p ON pd.peminjaman_id=p.id WHERE p.santri_id=s.id AND pd.status='dipinjam') as pinjam_aktif, (SELECT COALESCE(SUM(p.denda), 0) FROM peminjaman p WHERE p.santri_id=s.id) as total_denda FROM `santri` s WHERE " . implode(' AND ', $where) . " ORDER BY s.nama ASC";
        $stmt = $db->prepare($sql); $stmt->execute($params); $santriList = $stmt->fetchAll();
        if ($format === 'csv') {
            $this->exportCsv('laporan_santri_' . date('Ymd_His') . '.csv', ['NIS', 'NISN', 'Nama', 'JK', 'Kelas', 'Kamar', 'Tahun', 'Wali', 'HP', 'Pinjam Aktif', 'Denda', 'Status'], array_map(fn($s) => [$s['nis'], $s['nisn'], $s['nama'], $s['jenis_kelamin'], $s['kelas'], $s['kamar'], $s['tahun_masuk'], $s['nama_wali'], $s['no_hp'], $s['pinjam_aktif'], $s['total_denda'], $s['status']], $santriList));
            return;
        }
        $this->view('laporan.santri', ['pageTitle' => 'Laporan Keanggotaan Santri', 'santriList' => $santriList, 'kelasList' => $db->query("SELECT DISTINCT kelas FROM santri ORDER BY kelas ASC")->fetchAll(\PDO::FETCH_COLUMN), 'filter' => $filter, 'kelas' => $kelas, 'isPrint' => ($format === 'print')], ($format === 'print' ? false : 'main'));
    }
    public function peminjaman(Request $request): void {
        $db = Database::getConnection();
        $tglAwal = trim((string)$request->get('tgl_awal', date('Y-m-01')));
        $tglAkhir = trim((string)$request->get('tgl_akhir', date('Y-m-d')));
        $status = trim((string)$request->get('status', ''));
        $format = trim((string)$request->get('format', 'html'));
        $where = ["p.tanggal_pinjam BETWEEN :tgl_awal AND :tgl_akhir"];
        $params = ['tgl_awal' => $tglAwal, 'tgl_akhir' => $tglAkhir];
        if ($status !== '') { $where[] = "p.status = :status"; $params['status'] = $status; }
        $sql = "SELECT p.*, s.nis, s.nama as nama_santri, s.kelas, u.name as nama_petugas, GROUP_CONCAT(b.judul SEPARATOR ' | ') as daftar_buku, COUNT(pd.id) as jumlah_buku FROM `peminjaman` p JOIN `santri` s ON p.santri_id=s.id JOIN `users` u ON p.petugas_id=u.id LEFT JOIN `peminjaman_detail` pd ON p.id=pd.peminjaman_id LEFT JOIN `buku` b ON pd.buku_id=b.id WHERE " . implode(' AND ', $where) . " GROUP BY p.id ORDER BY p.tanggal_pinjam DESC";
        $stmt = $db->prepare($sql); $stmt->execute($params); $peminjamanList = $stmt->fetchAll();
        if ($format === 'csv') {
            $this->exportCsv('laporan_peminjaman_' . date('Ymd_His') . '.csv', ['Kode', 'Tgl Pinjam', 'Jatuh Tempo', 'NIS', 'Nama', 'Kelas', 'Jumlah', 'Buku', 'Status', 'Denda', 'Petugas'], array_map(fn($p) => [$p['kode_transaksi'], $p['tanggal_pinjam'], $p['tanggal_jatuh_tempo'], $p['nis'], $p['nama_santri'], $p['kelas'], $p['jumlah_buku'], $p['daftar_buku'], $p['status'], $p['denda'], $p['nama_petugas']], $peminjamanList));
            return;
        }
        $this->view('laporan.peminjaman', ['pageTitle' => 'Laporan Transaksi Peminjaman', 'peminjamanList' => $peminjamanList, 'tglAwal' => $tglAwal, 'tglAkhir' => $tglAkhir, 'status' => $status, 'isPrint' => ($format === 'print')], ($format === 'print' ? false : 'main'));
    }
    public function pengembalian(Request $request): void {
        $db = Database::getConnection();
        $tglAwal = trim((string)$request->get('tgl_awal', date('Y-m-01')));
        $tglAkhir = trim((string)$request->get('tgl_akhir', date('Y-m-d')));
        $format = trim((string)$request->get('format', 'html'));
        $sql = "SELECT pd.*, p.kode_transaksi, p.tanggal_pinjam, p.tanggal_jatuh_tempo, s.nis, s.nama as nama_santri, s.kelas, b.kode_buku, b.judul as judul_buku, u.name as nama_petugas FROM `peminjaman_detail` pd JOIN `peminjaman` p ON pd.peminjaman_id=p.id JOIN `santri` s ON p.santri_id=s.id JOIN `buku` b ON pd.buku_id=b.id JOIN `users` u ON p.petugas_id=u.id WHERE pd.tanggal_kembali BETWEEN :tgl_awal AND :tgl_akhir ORDER BY pd.tanggal_kembali DESC";
        $stmt = $db->prepare($sql); $stmt->execute(['tgl_awal' => $tglAwal, 'tgl_akhir' => $tglAkhir]);
        $kembaliList = $stmt->fetchAll();
        if ($format === 'csv') {
            $this->exportCsv('laporan_pengembalian_' . date('Ymd_His') . '.csv', ['Kode', 'NIS', 'Nama', 'Kode Buku', 'Judul', 'Pinjam', 'Tempo', 'Kembali', 'Kondisi', 'Denda', 'Petugas'], array_map(fn($k) => [$k['kode_transaksi'], $k['nis'], $k['nama_santri'], $k['kode_buku'], $k['judul_buku'], $k['tanggal_pinjam'], $k['tanggal_jatuh_tempo'], $k['tanggal_kembali'], $k['kondisi_kembali'], $k['denda'], $k['nama_petugas']], $kembaliList));
            return;
        }
        $this->view('laporan.pengembalian', ['pageTitle' => 'Laporan Pengembalian Buku & Denda', 'kembaliList' => $kembaliList, 'tglAwal' => $tglAwal, 'tglAkhir' => $tglAkhir, 'isPrint' => ($format === 'print')], ($format === 'print' ? false : 'main'));
    }
    protected function exportCsv(string $filename, array $headers, array $rows): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, $headers);
        foreach ($rows as $row) fputcsv($output, $row);
        fclose($output);
        exit;
    }
}
