<?php
namespace App\Core;
use PDO;
use PDOException;
use Throwable;
class Database {
    protected static ?PDO $instance = null;
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = config('database');
            $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s', $config['driver'] ?? 'mysql', $config['host'] ?? '127.0.0.1', $config['port'] ?? 3306, $config['database'] ?? 'perpustakaan', $config['charset'] ?? 'utf8mb4');
            try {
                self::$instance = new PDO($dsn, $config['username'] ?? 'root', $config['password'] ?? '', $config['options'] ?? []);
            } catch (PDOException $e) {
                if (config('app.debug', false)) {
                    throw new PDOException("Gagal terkoneksi ke database: " . $e->getMessage(), (int)$e->getCode());
                } else {
                    error_log("Database Connection Error: " . $e->getMessage());
                    die("Layanan database perpustakaan sedang tidak dapat diakses. Silakan hubungi administrator.");
                }
            }
        }
        return self::$instance;
    }
    public static function beginTransaction(): bool { return self::getConnection()->beginTransaction(); }
    public static function commit(): bool { return self::getConnection()->commit(); }
    public static function rollBack(): bool {
        if (self::getConnection()->inTransaction()) return self::getConnection()->rollBack();
        return false;
    }
    public static function transaction(callable $callback): mixed {
        self::beginTransaction();
        try {
            $result = $callback(self::getConnection());
            self::commit();
            return $result;
        } catch (Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }
}
