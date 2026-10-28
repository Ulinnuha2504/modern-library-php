<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller {
    public function index(): void {
        $db = Database::getConnection();
        $statBuku = $db->query("SELECT COUNT(*) as total_judul, COALESCE(SUM(jumlah_total),0) as total_eksemplar, COALESCE(SUM(jumlah_tersedia),0) as buku_tersedia, COALESCE(SUM(jumlah_dipinjam),0) as buku_dipinjam, COALESCE(SUM(jumlah_rusak),0) as buku_rusak, COALESCE(SUM(jumlah_hilang),0) as buku_hilang FROM `buku`")->fetch();
        $statSantri = $db->query("SELECT COUNT(*) as total_santri, COUNT(CASE WHEN status='aktif' THEN 1 END) as santri_aktif, (SELECT COUNT(DISTINCT p.santri_id) FROM `peminjaman` p JOIN `peminjaman_detail` pd ON p.id=pd.peminjaman_id WHERE pd.status='dipinjam') as santri_meminjam FROM `santri`")->fetch();
        $today = date('Y-m-d');
        $stmtTrx = $db->prepare("SELECT (SELECT COUNT(*) FROM `peminjaman` WHERE tanggal_pinjam=:t1) as pinjam_hari_ini, (SELECT COUNT(*) FROM `peminjaman_detail` WHERE tanggal_kembali=:t2) as kembali_hari_ini, (SELECT COUNT(*) FROM `peminjaman_detail` WHERE status='dipinjam') as sedang_dipinjam, (SELECT COUNT(DISTINCT p.id) FROM `peminjaman` p JOIN `peminjaman_detail` pd ON p.id=pd.peminjaman_id WHERE pd.status='dipinjam' AND p.tanggal_jatuh_tempo<:t3) as transaksi_terlambat");
        $stmtTrx->execute(['t1' => $today, 't2' => $today, 't3' => $today]);
        $statTrx = $stmtTrx->fetch();

        $chartBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $mStart = date('Y-m-01', strtotime("-$i months"));
            $mEnd = date('Y-m-t', strtotime("-$i months"));
            $mLabel = date('M Y', strtotime("-$i months"));
            $stmtM = $db->prepare("SELECT COUNT(pd.id) as tot FROM `peminjaman_detail` pd JOIN `peminjaman` p ON pd.peminjaman_id=p.id WHERE p.tanggal_pinjam BETWEEN :s AND :e");
            $stmtM->execute(['s' => $mStart, 'e' => $mEnd]);
            $chartBulanan[] = ['bulan' => $mLabel, 'total' => (int)($stmtM->fetch()['tot'] ?? 0)];
        }

        $topBooks = $db->query("SELECT b.judul, COUNT(pd.id) as total_dipinjam FROM `peminjaman_detail` pd JOIN `buku` b ON pd.buku_id=b.id GROUP BY b.id, b.judul ORDER BY total_dipinjam DESC LIMIT 10")->fetchAll();
        $kategoriChart = $db->query("SELECT k.nama as kategori, COUNT(pd.id) as total FROM `peminjaman_detail` pd JOIN `buku` b ON pd.buku_id=b.id JOIN `kategori` k ON b.kategori_id=k.id GROUP BY k.id, k.nama ORDER BY total DESC LIMIT 8")->fetchAll();
        $recentTransactions = $db->query("SELECT p.id, p.kode_transaksi, p.tanggal_pinjam, p.tanggal_jatuh_tempo, p.status, s.nama as nama_santri, s.nis, s.kelas, (SELECT COUNT(*) FROM `peminjaman_detail` pd WHERE pd.peminjaman_id=p.id) as total_buku FROM `peminjaman` p JOIN `santri` s ON p.santri_id=s.id ORDER BY p.id DESC LIMIT 6")->fetchAll();
        $stmtOvd = $db->prepare("SELECT p.id, p.kode_transaksi, p.tanggal_pinjam, p.tanggal_jatuh_tempo, s.nama as nama_santri, s.nis, s.kelas, s.no_hp, DATEDIFF(:t, p.tanggal_jatuh_tempo) as hari_terlambat FROM `peminjaman` p JOIN `santri` s ON p.santri_id=s.id WHERE p.status IN ('dipinjam','sebagian_dikembalikan','terlambat') AND p.tanggal_jatuh_tempo<:t2 ORDER BY p.tanggal_jatuh_tempo ASC LIMIT 5");
        $stmtOvd->execute(['t' => $today, 't2' => $today]);
        $overdueLoans = $stmtOvd->fetchAll();

        $this->view('dashboard.index', [
            'pageTitle' => 'Dashboard Utama', 'statBuku' => $statBuku, 'statSantri' => $statSantri,
            'statTrx' => $statTrx, 'chartBulanan' => $chartBulanan, 'topBooks' => $topBooks,
            'kategoriChart' => $kategoriChart, 'recentTransactions' => $recentTransactions, 'overdueLoans' => $overdueLoans
        ]);
    }
}
