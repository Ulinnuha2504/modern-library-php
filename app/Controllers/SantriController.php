<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Santri;
use App\Services\LogService;

class SantriController extends Controller {
    protected Santri $santriModel;
    public function __construct() { $this->santriModel = new Santri(); }
    public function index(Request $request): void {
        $page = (int) $request->get('page', 1);
        $search = trim((string)$request->get('q', ''));
        $kelas = trim((string)$request->get('kelas', ''));
        $kamar = trim((string)$request->get('kamar', ''));
        $status = trim((string)$request->get('status', ''));
        $where = ["1=1"]; $params = [];
        if ($search !== '') { $where[] = "(nis LIKE :kw1 OR nama LIKE :kw2 OR nisn LIKE :kw3)"; $params['kw1'] = "%$search%"; $params['kw2'] = "%$search%"; $params['kw3'] = "%$search%"; }
        if ($kelas !== '') { $where[] = "kelas = :kelas"; $params['kelas'] = $kelas; }
        if ($kamar !== '') { $where[] = "kamar = :kamar"; $params['kamar'] = $kamar; }
        if ($status !== '') { $where[] = "status = :status"; $params['status'] = $status; }
        $pagination = $this->santriModel->paginate($page, 15, implode(' AND ', $where), $params, 'nama ASC');
        $db = \App\Core\Database::getConnection();
        $kelasList = $db->query("SELECT DISTINCT kelas FROM `santri` ORDER BY kelas ASC")->fetchAll(\PDO::FETCH_COLUMN);
        $kamarList = $db->query("SELECT DISTINCT kamar FROM `santri` ORDER BY kamar ASC")->fetchAll(\PDO::FETCH_COLUMN);
        $this->view('santri.index', ['pageTitle' => 'Manajemen Data Santri', 'pagination' => $pagination, 'kelasList' => $kelasList, 'kamarList' => $kamarList, 'filters' => ['q' => $search, 'kelas' => $kelas, 'kamar' => $kamar, 'status' => $status]]);
    }
    public function detail(Request $request, string $id): void {
        $santri = $this->santriModel->find((int)$id);
        if (!$santri) { Session::setFlash('error', 'Santri tidak ditemukan.'); $this->redirect('santri'); }
        $history = $this->santriModel->getBorrowingHistory((int)$id);
        $this->view('santri.detail', ['pageTitle' => 'Profil Santri: ' . $santri['nama'], 'santri' => $santri, 'activeLoansCount' => $this->santriModel->getActiveLoansCount((int)$id), 'history' => $history, 'totalDenda' => array_sum(array_column($history, 'denda'))]);
    }
    public function create(): void { $this->view('santri.create', ['pageTitle' => 'Tambah Data Santri Baru']); }
    public function store(Request $request): void {
        $data = $this->validate($request, ['nis' => 'required|min:4|max:30', 'nama' => 'required|min:3|max:120', 'jenis_kelamin' => 'required', 'kelas' => 'required', 'kamar' => 'required', 'tahun_masuk' => 'required|numeric']);
        if ($this->santriModel->findByNis($data['nis'])) { Session::setFlash('error', "NIS sudah terdaftar."); $this->back(); }
        $this->santriModel->insert([
            'nis' => $data['nis'], 'nisn' => trim((string)$request->input('nisn', '')) ?: null, 'nama' => $data['nama'],
            'jenis_kelamin' => in_array($data['jenis_kelamin'], ['L', 'P']) ? $data['jenis_kelamin'] : 'L', 'kelas' => $data['kelas'],
            'kamar' => $data['kamar'], 'tahun_masuk' => (int)$data['tahun_masuk'], 'nama_wali' => trim((string)$request->input('nama_wali', '')) ?: null,
            'no_hp' => trim((string)$request->input('no_hp', '')) ?: null, 'alamat' => trim((string)$request->input('alamat', '')) ?: null, 'status' => 'aktif'
        ]);
        LogService::record('TAMBAH_SANTRI', 'SANTRI', "Tambah santri {$data['nama']} ({$data['nis']})");
        Session::setFlash('success', 'Data santri berhasil ditambahkan.');
        $this->redirect('santri');
    }
    public function edit(Request $request, string $id): void {
        $santri = $this->santriModel->find((int)$id);
        if (!$santri) { Session::setFlash('error', 'Santri tidak ditemukan.'); $this->redirect('santri'); }
        $this->view('santri.edit', ['pageTitle' => 'Ubah Data Santri', 'santri' => $santri]);
    }
    public function update(Request $request, string $id): void {
        $santri = $this->santriModel->find((int)$id);
        if (!$santri) { Session::setFlash('error', 'Santri tidak ditemukan.'); $this->redirect('santri'); }
        $data = $this->validate($request, ['nama' => 'required', 'jenis_kelamin' => 'required', 'kelas' => 'required', 'kamar' => 'required', 'tahun_masuk' => 'required|numeric', 'status' => 'required']);
        $this->santriModel->update((int)$id, [
            'nisn' => trim((string)$request->input('nisn', '')) ?: null, 'nama' => $data['nama'], 'jenis_kelamin' => $data['jenis_kelamin'],
            'kelas' => $data['kelas'], 'kamar' => $data['kamar'], 'tahun_masuk' => (int)$data['tahun_masuk'], 'nama_wali' => trim((string)$request->input('nama_wali', '')) ?: null,
            'no_hp' => trim((string)$request->input('no_hp', '')) ?: null, 'alamat' => trim((string)$request->input('alamat', '')) ?: null, 'status' => $data['status']
        ]);
        Session::setFlash('success', 'Data santri diperbarui.');
        $this->redirect('santri/detail/' . $id);
    }
    public function searchAjax(Request $request): void {
        $q = trim((string)$request->get('q', ''));
        if (strlen($q) < 1) $this->json([]);
        $results = $this->santriModel->search($q, 10);
        foreach ($results as &$s) { $s['pinjam_aktif'] = $this->santriModel->getActiveLoansCount((int)$s['id']); }
        $this->json($results);
    }
}
