<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
class PetugasMiddleware {
    public function handle(Request $request): void {
        $user = Session::get('user');
        $role = $user['role'] ?? '';
        if (!$user || !in_array($role, ['admin', 'petugas'], true)) {
            Session::setFlash('error', 'Akses ditolak. Fitur transaksi sirkulasi hanya untuk Petugas atau Administrator.');
            if ($request->isAjax()) Response::json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
            Response::redirect(url('dashboard'));
        }
    }
}
