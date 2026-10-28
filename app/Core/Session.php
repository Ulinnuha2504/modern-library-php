<?php
namespace App\Core;
class Session {
    protected static bool $started = false;
    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) { self::$started = true; return; }
        $lifetime = (int) (config('app.session_lifetime', 120) * 60);
        session_set_cookie_params([
            'lifetime' => $lifetime, 'path' => '/', 'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true, 'samesite' => 'Lax'
        ]);
        session_start();
        self::$started = true;
        if (!isset($_SESSION['_flash'])) $_SESSION['_flash'] = [];
        if (!isset($_SESSION['_flash_next'])) $_SESSION['_flash_next'] = [];
        $_SESSION['_flash'] = $_SESSION['_flash_next'];
        $_SESSION['_flash_next'] = [];
    }
    public static function set(string $key, mixed $value): void { self::start(); $_SESSION[$key] = $value; }
    public static function get(string $key, mixed $default = null): mixed { self::start(); return $_SESSION[$key] ?? $default; }
    public static function has(string $key): bool { self::start(); return isset($_SESSION[$key]); }
    public static function remove(string $key): void { self::start(); unset($_SESSION[$key]); }
    public static function setFlash(string $key, mixed $value): void { self::start(); $_SESSION['_flash_next'][$key] = $value; }
    public static function getFlash(string $key, mixed $default = null): mixed { self::start(); return $_SESSION['_flash'][$key] ?? $default; }
    public static function hasFlash(string $key): bool { self::start(); return isset($_SESSION['_flash'][$key]); }
    public static function csrfToken(): string {
        self::start();
        if (empty($_SESSION['_csrf_token'])) $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf_token'];
    }
    public static function validateCsrfToken(?string $token): bool {
        self::start();
        $stored = $_SESSION['_csrf_token'] ?? '';
        if (empty($stored) || empty($token)) return false;
        return hash_equals($stored, $token);
    }
    public static function regenerate(): void { self::start(); session_regenerate_id(true); }
    public static function destroy(): void {
        self::start(); $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        self::$started = false;
    }
}
