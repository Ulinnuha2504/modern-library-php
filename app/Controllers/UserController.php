<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;
use App\Services\LogService;

class UserController extends Controller {
    protected User $userModel;
    public function __construct() { $this->userModel = new User(); }
    public function index(): void {
        $this->view('users.index', ['pageTitle' => 'Manajemen Pengguna & Petugas', 'users' => $this->userModel->all('id ASC')]);
    }
    public function store(Request $request): void {
        $data = $this->validate($request, ['name' => 'required', 'email' => 'required|email', 'username' => 'required', 'password' => 'required|min:6', 'role' => 'required']);
        if ($this->userModel->firstWhere('username', $data['username'])) { Session::setFlash('error', "Username sudah digunakan."); $this->redirect('users'); }
        $this->userModel->insert(['name' => $data['name'], 'email' => $data['email'], 'username' => $data['username'], 'password' => password_hash($data['password'], PASSWORD_BCRYPT), 'role' => $data['role'], 'status' => 'aktif']);
        LogService::record('TAMBAH_USER', 'PENGGUNA', "Tambah pengguna @{$data['username']}");
        Session::setFlash('success', 'Pengguna baru berhasil ditambahkan.');
        $this->redirect('users');
    }
    public function update(Request $request, string $id): void {
        $user = $this->userModel->find((int)$id);
        if (!$user) { Session::setFlash('error', 'User tidak ditemukan.'); $this->redirect('users'); }
        $data = $this->validate($request, ['name' => 'required', 'email' => 'required|email', 'role' => 'required', 'status' => 'required']);
        $payload = ['name' => $data['name'], 'email' => $data['email'], 'role' => $data['role'], 'status' => $data['status']];
        $newPw = trim((string)$request->input('password', ''));
        if ($newPw !== '') $payload['password'] = password_hash($newPw, PASSWORD_BCRYPT);
        $this->userModel->update((int)$id, $payload);
        Session::setFlash('success', 'Data pengguna diperbarui.');
        $this->redirect('users');
    }
    public function toggleStatus(Request $request, string $id): void {
        $curr = Session::get('user');
        if ((int)$id === (int)($curr['id'] ?? 0)) { Session::setFlash('error', 'Tidak dapat menonaktifkan akun sendiri.'); $this->redirect('users'); }
        $user = $this->userModel->find((int)$id);
        $new = ($user['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        $this->userModel->update((int)$id, ['status' => $new]);
        Session::setFlash('success', "Status akun diubah menjadi " . strtoupper($new));
        $this->redirect('users');
    }
}
