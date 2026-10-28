<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Services\LogService;

class BukuController extends Controller {
    protected Buku $bukuModel;
    protected Kategori $kategoriModel;
    protected Rak $rakModel;
    public function __construct() {
        $this->bukuModel = new Buku();
        $this->kategoriModel = new Kategori();
        $this->rakModel = new Rak();
    }
    public function index(Request $request): void {
        $page = (int) $request->get('page', 1);
        $filters = ['search' => $request->get('q', ''), 'kategori_id' => $request->get('kategori', ''), 'rak_id' => $request->get('rak', ''), 'status' => $request->get('status', '')];
        $this->view('buku.index', ['pageTitle' => 'Katalog Data Buku', 'pagination' => $this->bukuModel->paginateWithRelations($page, 12, $filters), 'kategoriList' => $this->kategoriModel->allActive(), 'rakList' => $this->rakModel->allActive(), 'filters' => $filters]);
    }
    public function detail(Request $request, string $id): void {
        $buku = $this->bukuModel->findWithRelations((int)$id);
        if (!$buku) { Session::setFlash('error', 'Buku tidak ditemukan.'); $this->redirect('buku'); }
        $this->view('buku.detail', ['pageTitle' => 'Detail Buku: ' . $buku['judul'], 'buku' => $buku, 'borrowHistory' => $this->bukuModel->getBorrowHistory((int)$id)]);
    }
    public function create(): void {
        $this->view('buku.create', ['pageTitle' => 'Tambah Buku Baru', 'kategoriList' => $this->kategoriModel->allActive(), 'rakList' => $this->rakModel->allActive(), 'generatedKode' => $this->bukuModel->generateKodeBuku()]);
    }
    public function store(Request $request): void {
        $data = $this->validate($request, ['kode_buku' => 'required|min:3|max:50', 'judul' => 'required|min:2|max:255', 'penulis' => 'required', 'penerbit' => 'required', 'tahun_terbit' => 'required|numeric', 'kategori_id' => 'required|numeric', 'rak_id' => 'required|numeric', 'jumlah_total' => 'required|numeric']);
        if ($this->bukuModel->firstWhere('kode_buku', $data['kode_buku'])) { Session::setFlash('error', "Kode buku sudah ada."); $this->back(); }
        $isbn = trim((string)$request->input('isbn', ''));
        $jumlahTotal = max(0, (int)$data['jumlah_total']);
        $coverName = null;
        $coverFile = $request->file('cover');
        if ($coverFile && $coverFile['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($coverFile['name'], PATHINFO_EXTENSION);
            $coverName = 'cover_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            move_uploaded_file($coverFile['tmp_name'], __DIR__ . '/../../public/uploads/covers/' . $coverName);
        }
        $this->bukuModel->insert([
            'kode_buku' => $data['kode_buku'], 'isbn' => $isbn ?: null, 'judul' => $data['judul'], 'penulis' => $data['penulis'],
            'penerbit' => $data['penerbit'], 'tahun_terbit' => (int)$data['tahun_terbit'], 'kategori_id' => (int)$data['kategori_id'],
            'rak_id' => (int)$data['rak_id'], 'jumlah_total' => $jumlahTotal, 'jumlah_tersedia' => $jumlahTotal,
            'jumlah_dipinjam' => 0, 'jumlah_rusak' => 0, 'jumlah_hilang' => 0, 'cover' => $coverName,
            'deskripsi' => trim((string)$request->input('deskripsi', '')), 'status' => 'aktif'
        ]);
        LogService::record('TAMBAH_BUKU', 'BUKU', "Menambahkan buku: {$data['judul']}");
        Session::setFlash('success', 'Buku baru berhasil ditambahkan.');
        $this->redirect('buku');
    }
    public function edit(Request $request, string $id): void {
        $buku = $this->bukuModel->find((int)$id);
        if (!$buku) { Session::setFlash('error', 'Buku tidak ditemukan.'); $this->redirect('buku'); }
        $this->view('buku.edit', ['pageTitle' => 'Ubah Data Buku', 'buku' => $buku, 'kategoriList' => $this->kategoriModel->allActive(), 'rakList' => $this->rakModel->allActive()]);
    }
    public function update(Request $request, string $id): void {
        $buku = $this->bukuModel->find((int)$id);
        if (!$buku) { Session::setFlash('error', 'Buku tidak ditemukan.'); $this->redirect('buku'); }
        $data = $this->validate($request, ['judul' => 'required', 'penulis' => 'required', 'penerbit' => 'required', 'tahun_terbit' => 'required|numeric', 'kategori_id' => 'required|numeric', 'rak_id' => 'required|numeric']);
        $tambah = max(0, (int)($request->input('tambah_stok') ?? 0));
        $coverName = $buku['cover'];
        $coverFile = $request->file('cover');
        if ($coverFile && $coverFile['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($coverFile['name'], PATHINFO_EXTENSION);
            $coverName = 'cover_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            move_uploaded_file($coverFile['tmp_name'], __DIR__ . '/../../public/uploads/covers/' . $coverName);
        }
        $this->bukuModel->update((int)$id, [
            'isbn' => trim((string)$request->input('isbn', '')) ?: null, 'judul' => $data['judul'], 'penulis' => $data['penulis'],
            'penerbit' => $data['penerbit'], 'tahun_terbit' => (int)$data['tahun_terbit'], 'kategori_id' => (int)$data['kategori_id'],
            'rak_id' => (int)$data['rak_id'], 'jumlah_total' => $buku['jumlah_total'] + $tambah, 'jumlah_tersedia' => $buku['jumlah_tersedia'] + $tambah,
            'cover' => $coverName, 'deskripsi' => trim((string)$request->input('deskripsi', '')),
            'status' => in_array($request->input('status'), ['aktif', 'nonaktif'], true) ? $request->input('status') : $buku['status'],
        ]);
        LogService::record('EDIT_BUKU', 'BUKU', "Ubah buku {$buku['kode_buku']}");
        Session::setFlash('success', 'Data buku berhasil diperbarui.');
        $this->redirect('buku/detail/' . $id);
    }
    public function toggleStatus(Request $request, string $id): void {
        $buku = $this->bukuModel->find((int)$id);
        $new = ($buku['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        $this->bukuModel->update((int)$id, ['status' => $new]);
        Session::setFlash('success', "Status buku diubah menjadi " . strtoupper($new));
        $this->redirect('buku');
    }
    public function searchAjax(Request $request): void {
        $q = trim((string)$request->get('q', ''));
        $this->json(strlen($q) ? $this->bukuModel->searchAvailable($q, 10) : []);
    }
}
