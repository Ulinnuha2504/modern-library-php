<?php
use App\Core\Env;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed {
        static $configs = [];
        $parts = explode('.', $key, 2);
        $file = $parts[0];
        $item = $parts[1] ?? null;
        if (!isset($configs[$file])) {
            $configPath = __DIR__ . '/../../config/' . $file . '.php';
            $configs[$file] = file_exists($configPath) ? require $configPath : [];
        }
        return ($item === null) ? $configs[$file] : ($configs[$file][$item] ?? $default);
    }
}
if (!function_exists('url')) {
    function url(string $path = ''): string {
        $baseUrl = rtrim(config('app.url', 'http://localhost/perpustakaan/public'), '/');
        $path = ltrim($path, '/');
        return $path === '' ? $baseUrl : $baseUrl . '/' . $path;
    }
}
if (!function_exists('asset')) {
    function asset(string $path): string { return url('assets/' . ltrim($path, '/')); }
}
if (!function_exists('view')) {
    function view(string $view, array $data = []): void { View::render($view, $data); }
}
if (!function_exists('redirect')) {
    function redirect(string $path): void { Response::redirect(url($path)); }
}
if (!function_exists('json_response')) {
    function json_response(mixed $data, int $status = 200): void { Response::json($data, $status); }
}
if (!function_exists('csrf_token')) {
    function csrf_token(): string { return Session::csrfToken(); }
}
if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed {
        $oldInputs = Session::getFlash('_old_inputs', []);
        return $oldInputs[$key] ?? $default;
    }
}
if (!function_exists('auth_user')) {
    function auth_user(): ?array { return Session::get('user'); }
}
if (!function_exists('is_role')) {
    function is_role(string|array $roles): bool {
        $user = auth_user();
        if (!$user) return false;
        return in_array($user['role'] ?? '', (array)$roles, true);
    }
}
if (!function_exists('flash')) {
    function flash(string $key, mixed $value): void { Session::setFlash($key, $value); }
}
if (!function_exists('get_flash')) {
    function get_flash(string $key, mixed $default = null): mixed { return Session::getFlash($key, $default); }
}
if (!function_exists('has_flash')) {
    function has_flash(string $key): bool { return Session::hasFlash($key); }
}
if (!function_exists('format_rupiah')) {
    function format_rupiah(float|int|string|null $nominal): string {
        return 'Rp ' . number_format((float)($nominal ?? 0), 0, ',', '.');
    }
}
if (!function_exists('format_tanggal')) {
    function format_tanggal(?string $datetime, bool $withTime = false): string {
        if (!$datetime) return '-';
        $ts = strtotime($datetime);
        if (!$ts) return '-';
        $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $res = date('d', $ts) . ' ' . $bulanIndo[(int)date('m', $ts)] . ' ' . date('Y', $ts);
        return $withTime ? $res . ' ' . date('H:i', $ts) . ' WIB' : $res;
    }
}
if (!function_exists('sanitize')) {
    function sanitize(?string $str): string { return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8'); }
}
