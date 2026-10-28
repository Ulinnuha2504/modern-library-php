<?php
namespace App\Core;
class Response {
    public static function setStatusCode(int $code): void { http_response_code($code); }
    public static function json(mixed $data, int $status = 200): void {
        self::setStatusCode($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    public static function redirect(string $url, int $status = 302): void {
        self::setStatusCode($status);
        header('Location: ' . $url);
        exit;
    }
}
