<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
class AuthMiddleware {
    public function handle(Request $request): void {
        if (!Session::has('user')) {
            Session::setFlash('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
            if ($request->isAjax()) Response::json(['status' => 'error', 'message' => 'Sesi Anda telah berakhir.'], 401);
            Response::redirect(url('login'));
        }
    }
}
