<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Kategori;
use App\Models\Buku;
use App\Services\LogService;

class KategoriController extends Controller {
    protected Kategori $kategoriModel;
    public function __construct() { $this->kategoriModel = new Kategori(); }
    public function index(): void {
        $this->view('kategori.index', ['pageTitle' => 'Manajemen Kategori Buku', 'kategoriList' => $this->kategoriModel->getWithBookCount()]);
    }
    public function store(Request $request): void {
        $data = $this->validate($request, ['nama' => 'required']);
        if ($this->kategoriModel->firstWhere('nama', $data['nama'])) { Session::setFlash('error', "Kategori '{$data['nama']}' sudah ada."); $this->redirect('kategori'); }
        $this->kategoriModel->insert(['nama' => $data['nama'], 'deskripsi' => trim((string)$request->input('deskripsi', '')) ?: null, 'status' => 'aktif']);
        LogService::record('TAMBAH_KATEGORI', 'KATEGORI', "Tambah kategori {$data['nama']}");
        Session::setFlash('success', 'Kategori berhasil ditambahkan.');
        $this->redirect('kategori');
    }
    public function update(Request $request, string $id): void {
        $data = $this->validate($request, ['nama' => 'required', 'status' => 'required']);
        $this->kategoriModel->update((int)$id, ['nama' => $data['nama'], 'deskripsi' => trim((string)$request->input('deskripsi', '')) ?: null, 'status' => $data['status']]);
        Session::setFlash('success', 'Kategori diperbarui.');
        $this->redirect('kategori');
    }
    public function delete(Request $request, string $id): void {
        if ((new Buku())->count("kategori_id = :id", ['id' => (int)$id]) > 0) {
            Session::setFlash('error', 'Kategori tidak dapat dihapus karena masih menampung buku.');
            $this->redirect('kategori');
        }
        $this->kategoriModel->delete((int)$id);
        Session::setFlash('success', 'Kategori berhasil dihapus.');
        $this->redirect('kategori');
    }
}
