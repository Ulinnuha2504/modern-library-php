<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Setting;
use App\Services\SirkulasiService;
use Throwable;

class PengembalianController extends Controller {
    protected Peminjaman $peminjamanModel;
    protected SirkulasiService $sirkulasiService;
    public function __construct() { $this->peminjamanModel = new Peminjaman(); $this->sirkulasiService = new SirkulasiService(); }
    public function index(Request $request): void {
        $settingModel = new Setting();
        $kode = trim((string)$request->get('kode', ''));
        $initialTrx = null;
        if ($kode !== '') {
            $initialTrx = $this->peminjamanModel->findByKodeTransaksi($kode);
            if ($initialTrx) $initialTrx['items'] = (new PeminjamanDetail())->getDetailsByPeminjamanId((int)$initialTrx['id']);
        }
        $this->view('transaksi.pengembalian', [
            'pageTitle' => 'Layanan Pengembalian Buku & Denda', 'dendaPerHari' => (float)$settingModel->get('denda_per_hari', 1000),
            'dendaRusakRingan' => (float)$settingModel->get('denda_rusak_ringan', 5000), 'dendaRusakBerat' => (float)$settingModel->get('denda_rusak_berat', 20000),
            'initialTrx' => $initialTrx, 'kodeSearch' => $kode
        ]);
    }
    public function searchAjax(Request $request): void {
        $query = trim((string)$request->get('q', ''));
        if (strlen($query) < 2) $this->json(['status' => 'error', 'message' => 'Minimal 2 karakter.']);
        $db = \App\Core\Database::getConnection();
        $kw = "%$query%";
        $stmt = $db->prepare("SELECT p.id, p.kode_transaksi, p.tanggal_pinjam, p.tanggal_jatuh_tempo, p.status, s.nis, s.nama as nama_santri, s.kelas, s.kamar, s.no_hp FROM `peminjaman` p JOIN `santri` s ON p.santri_id=s.id WHERE p.status IN ('dipinjam','sebagian_dikembalikan','terlambat') AND (p.kode_transaksi LIKE :kw1 OR s.nis LIKE :kw2 OR s.nama LIKE :kw3) ORDER BY p.tanggal_jatuh_tempo ASC LIMIT 10");
        $stmt->execute(['kw1' => $kw, 'kw2' => $kw, 'kw3' => $kw]);
        $list = $stmt->fetchAll();
        $today = date('Y-m-d');
        $dendaPerHari = (float)(new Setting())->get('denda_per_hari', 1000);
        foreach ($list as &$trx) {
            $ht = (strtotime($today) > strtotime($trx['tanggal_jatuh_tempo'])) ? (int)floor((strtotime($today) - strtotime($trx['tanggal_jatuh_tempo'])) / 86400) : 0;
            $trx['hari_terlambat'] = $ht;
            $trx['estimasi_denda_per_buku'] = $ht * $dendaPerHari;
            $stmtDet = $db->prepare("SELECT pd.id as detail_id, pd.buku_id, pd.kondisi_pinjam, b.kode_buku, b.judul as judul_buku, b.penulis, b.cover, k.nama as kategori FROM `peminjaman_detail` pd JOIN `buku` b ON pd.buku_id=b.id LEFT JOIN `kategori` k ON b.kategori_id=k.id WHERE pd.peminjaman_id=:id AND pd.status='dipinjam'");
            $stmtDet->execute(['id' => $trx['id']]);
            $trx['unreturned_books'] = $stmtDet->fetchAll();
        }
        $this->json(['status' => 'success', 'data' => $list]);
    }
    public function process(Request $request): void {
        $pId = (int)$request->input('peminjaman_id');
        $items = $request->input('items');
        $user = Session::get('user');
        $petugasId = (int)($user['id'] ?? 1);
        if ($pId <= 0 || empty($items) || !is_array($items)) {
            if ($request->isAjax()) $this->json(['status' => 'error', 'message' => 'Pilih minimal satu buku.'], 422);
            Session::setFlash('error', 'Pilih minimal satu buku.'); $this->back();
        }
        try {
            $res = $this->sirkulasiService->prosesPengembalian($pId, $items, $petugasId);
            $msg = "Pengembalian berhasil diproses ({$res['items_processed']} buku). Total denda: " . format_rupiah($res['denda_transaksi']);
            if ($request->isAjax()) $this->json(['status' => 'success', 'message' => $msg, 'data' => $res]);
            Session::setFlash('success', $msg);
            $this->redirect('transaksi/detail/' . $pId);
        } catch (Throwable $e) {
            if ($request->isAjax()) $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
            Session::setFlash('error', $e->getMessage()); $this->back();
        }
    }
}
