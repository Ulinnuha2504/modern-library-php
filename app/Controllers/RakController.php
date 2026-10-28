<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Rak;
use App\Models\Buku;
use App\Services\LogService;

class RakController extends Controller {
    protected Rak $rakModel;
    public function __construct() { $this->rakModel = new Rak(); }
    public function index(): void {
        $this->view('rak.index', ['pageTitle' => 'Manajemen Rak Buku', 'rakList' => $this->rakModel->getWithBookCount()]);
    }
    public function store(Request $request): void {
        $data = $this->validate($request, ['kode_rak' => 'required', 'nama_rak' => 'required', 'lokasi' => 'required']);
        if ($this->rakModel->firstWhere('kode_rak', $data['kode_rak'])) { Session::setFlash('error', "Kode rak sudah ada."); $this->redirect('rak'); }
        $this->rakModel->insert(['kode_rak' => strtoupper($data['kode_rak']), 'nama_rak' => $data['nama_rak'], 'lokasi' => $data['lokasi'], 'deskripsi' => trim((string)$request->input('deskripsi', '')) ?: null, 'status' => 'aktif']);
        Session::setFlash('success', 'Rak berhasil ditambahkan.');
        $this->redirect('rak');
    }
    public function update(Request $request, string $id): void {
        $data = $this->validate($request, ['nama_rak' => 'required', 'lokasi' => 'required', 'status' => 'required']);
        $this->rakModel->update((int)$id, ['nama_rak' => $data['nama_rak'], 'lokasi' => $data['lokasi'], 'deskripsi' => trim((string)$request->input('deskripsi', '')) ?: null, 'status' => $data['status']]);
        Session::setFlash('success', 'Rak diperbarui.');
        $this->redirect('rak');
    }
    public function delete(Request $request, string $id): void {
        if ((new Buku())->count("rak_id = :id", ['id' => (int)$id]) > 0) {
            Session::setFlash('error', 'Rak tidak dapat dihapus karena masih menampung buku.');
            $this->redirect('rak');
        }
        $this->rakModel->delete((int)$id);
        Session::setFlash('success', 'Rak berhasil dihapus.');
        $this->redirect('rak');
    }
}
