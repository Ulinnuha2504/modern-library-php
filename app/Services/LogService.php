<?php
namespace App\Services;
use App\Core\Session;
use App\Models\ActivityLog;
class LogService {
    public static function record(string $action, string $module, string $description, ?int $userId = null): void {
        try {
            if ($userId === null) {
                $user = Session::get('user');
                $userId = $user['id'] ?? null;
            }
            $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 250);
            (new ActivityLog())->insert([
                'user_id' => $userId, 'action' => strtoupper($action), 'module' => strtoupper($module),
                'description' => $description, 'ip_address' => $ip, 'user_agent' => $userAgent
            ]);
        } catch (\Throwable $e) { error_log("Log error: " . $e->getMessage()); }
    }
}
