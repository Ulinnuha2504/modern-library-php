<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Setting;
use App\Services\LogService;

class SettingController extends Controller {
    protected Setting $settingModel;
    public function __construct() { $this->settingModel = new Setting(); }
    public function index(): void {
        $this->view('settings.index', ['pageTitle' => 'Pengaturan Konfigurasi Sistem', 'settings' => $this->settingModel->getAllKeyed()]);
    }
    public function update(Request $request): void {
        $keys = ['nama_perpustakaan', 'alamat_perpustakaan', 'telepon_perpustakaan', 'email_perpustakaan', 'denda_per_hari', 'maksimal_pinjaman', 'lama_peminjaman', 'denda_rusak_ringan', 'denda_rusak_berat'];
        foreach ($keys as $k) {
            $v = trim((string)$request->input($k, ''));
            if ($v !== '') $this->settingModel->set($k, $v);
        }
        LogService::record('UPDATE_SETTINGS', 'PENGATURAN', 'Ubah pengaturan sirkulasi');
        Session::setFlash('success', 'Konfigurasi sistem berhasil disimpan.');
        $this->redirect('pengaturan');
    }
}
