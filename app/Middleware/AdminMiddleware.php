<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
class AdminMiddleware {
    public function handle(Request $request): void {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            Session::setFlash('error', 'Akses ditolak. Tindakan ini memerlukan hak akses Administrator.');
            if ($request->isAjax()) Response::json(['status' => 'error', 'message' => 'Akses ditolak. Khusus Administrator.'], 403);
            Response::redirect(url('dashboard'));
        }
    }
}
