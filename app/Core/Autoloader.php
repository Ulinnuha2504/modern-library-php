<?php
namespace App\Core;
class Autoloader {
    protected string $prefix = 'App\\';
    protected string $baseDir;
    public function __construct(string $baseDir) { $this->baseDir = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR; }
    public function register(): void { spl_autoload_register([$this, 'loadClass']); }
    public function loadClass(string $class): bool {
        $len = strlen($this->prefix);
        if (strncmp($this->prefix, $class, $len) !== 0) return false;
        $relativeClass = substr($class, $len);
        $file = $this->baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        if (file_exists($file)) { require_once $file; return true; }
        return false;
    }
}
