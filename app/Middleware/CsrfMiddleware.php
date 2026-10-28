<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
class CsrfMiddleware {
    public function handle(Request $request): void {
        $method = $request->method();
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
            if (!Session::validateCsrfToken($token)) {
                if ($request->isAjax()) Response::json(['status' => 'error', 'message' => 'Token CSRF tidak valid atau sesi telah kadaluwarsa.'], 419);
                Session::setFlash('error', 'Sesi formulir kadaluwarsa. Silakan ulangi pengisian formulir.');
                Response::redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
            }
        }
    }
}
