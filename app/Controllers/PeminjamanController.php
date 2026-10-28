<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Peminjaman;
use App\Models\Setting;
use App\Services\SirkulasiService;
use Throwable;

class PeminjamanController extends Controller {
    protected Peminjaman $peminjamanModel;
    protected SirkulasiService $sirkulasiService;
    public function __construct() { $this->peminjamanModel = new Peminjaman(); $this->sirkulasiService = new SirkulasiService(); }
    public function index(Request $request): void {
        $page = (int) $request->get('page', 1);
        $filters = ['search' => $request->get('q', ''), 'status' => $request->get('status', ''), 'tanggal_awal' => $request->get('tgl_awal', ''), 'tanggal_akhir' => $request->get('tgl_akhir', '')];
        $this->view('transaksi.index', ['pageTitle' => 'Riwayat Transaksi Peminjaman', 'pagination' => $this->peminjamanModel->paginateWithRelations($page, 15, $filters), 'filters' => $filters]);
    }
    public function create(): void {
        $settingModel = new Setting();
        $this->view('transaksi.peminjaman', ['pageTitle' => 'Formulir Peminjaman Buku', 'maksimalPinjaman' => (int)$settingModel->get('maksimal_pinjaman', 3), 'lamaPeminjaman' => (int)$settingModel->get('lama_peminjaman', 7)]);
    }
    public function store(Request $request): void {
        $santriId = (int) $request->input('santri_id');
        $bukuIds = (array) $request->input('buku_ids', []);
        $catatan = trim((string)$request->input('catatan', ''));
        $user = Session::get('user');
        $petugasId = (int) ($user['id'] ?? 1);
        if ($santriId <= 0 || empty($bukuIds)) { Session::setFlash('error', 'Lengkapi pilihan santri dan buku.'); $this->back(); }
        try {
            $res = $this->sirkulasiService->pinjamBuku($santriId, $bukuIds, $catatan, $petugasId);
            Session::setFlash('success', "Transaksi berhasil dibuat! Kode: {$res['kode_transaksi']}");
            $this->redirect('transaksi/detail/' . $res['peminjaman_id']);
        } catch (Throwable $e) { Session::setFlash('error', $e->getMessage()); $this->back(); }
    }
    public function detail(Request $request, string $id): void {
        $trx = $this->peminjamanModel->findWithFullDetails((int)$id);
        if (!$trx) { Session::setFlash('error', 'Transaksi tidak ditemukan.'); $this->redirect('transaksi'); }
        $settingModel = new Setting();
        $today = date('Y-m-d');
        $hariTerlambat = (in_array($trx['status'], ['dipinjam', 'sebagian_dikembalikan'], true) && strtotime($today) > strtotime($trx['tanggal_jatuh_tempo'])) ? (int)floor((strtotime($today) - strtotime($trx['tanggal_jatuh_tempo'])) / 86400) : 0;
        $this->view('transaksi.detail', ['pageTitle' => 'Detail Transaksi ' . $trx['kode_transaksi'], 'transaksi' => $trx, 'dendaPerHari' => (float)$settingModel->get('denda_per_hari', 1000), 'hariTerlambat' => $hariTerlambat]);
    }
}
