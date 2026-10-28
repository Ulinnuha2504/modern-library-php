<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;
use App\Services\LogService;

class AuthController extends Controller {
    public function showLoginForm(): void {
        if (Session::has('user')) $this->redirect('dashboard');
        $this->view('auth.login', ['pageTitle' => 'Masuk Sistem Perpustakaan'], false);
    }
    public function login(Request $request): void {
        $data = $this->validate($request, ['username' => 'required', 'password' => 'required']);
        $userModel = new User();
        $user = $userModel->findByUsernameOrEmail($data['username']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Session::setFlash('error', 'Kombinasi nama pengguna / email dan kata sandi tidak cocok.');
            Session::setFlash('_old_inputs', ['username' => $data['username']]);
            $this->redirect('login');
        }
        if ($user['status'] !== 'aktif') {
            Session::setFlash('error', 'Akun Anda berstatus nonaktif.');
            $this->redirect('login');
        }
        Session::regenerate();
        Session::set('user', ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email'], 'username' => $user['username'], 'role' => $user['role']]);
        $userModel->updateLastLogin((int)$user['id']);
        LogService::record('LOGIN', 'AUTENTIKASI', "Pengguna {$user['name']} masuk", (int)$user['id']);
        Session::setFlash('success', "Selamat datang kembali, {$user['name']}!");
        $this->redirect('dashboard');
    }
    public function logout(): void {
        $user = Session::get('user');
        if ($user) LogService::record('LOGOUT', 'AUTENTIKASI', "Pengguna {$user['name']} keluar", (int)$user['id']);
        Session::destroy();
        $this->redirect('login');
    }
}
