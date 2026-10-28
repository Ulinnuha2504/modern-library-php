<?php
namespace App\Core;
class Request {
    protected array $query;
    protected array $body;
    protected array $server;
    protected array $files;
    protected ?array $json = null;
    public function __construct() {
        $this->query = $_GET;
        $this->body = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $this->json = json_decode($raw, true) ?: [];
        }
    }
    public function method(): string {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->body['_method'])) return strtoupper($this->body['_method']);
        return $method;
    }
    public function uri(): string { return $this->server['REQUEST_URI'] ?? '/'; }
    public function path(): string {
        $uri = parse_url($this->uri(), PHP_URL_PATH) ?? '/';
        $scriptName = dirname($this->server['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && str_starts_with($uri, $scriptName)) {
            $uri = substr($uri, strlen($scriptName));
        }
        $path = '/' . trim($uri, '/');
        return $path === '' ? '/' : $path;
    }
    public function get(string $key, mixed $default = null): mixed { return $this->query[$key] ?? $default; }
    public function post(string $key, mixed $default = null): mixed { return $this->body[$key] ?? $default; }
    public function input(string $key, mixed $default = null): mixed {
        if ($this->json !== null && array_key_exists($key, $this->json)) return $this->json[$key];
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }
    public function all(): array {
        if ($this->json !== null) return array_merge($this->query, $this->json);
        return array_merge($this->query, $this->body);
    }
    public function isAjax(): bool {
        $requestedWith = $this->server['HTTP_X_REQUESTED_WITH'] ?? '';
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return strtolower($requestedWith) === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }
    public function header(string $name, mixed $default = null): mixed {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$normalized] ?? $this->server[$name] ?? $default;
    }
    public function ip(): string { return $this->server['HTTP_CLIENT_IP'] ?? $this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '127.0.0.1'; }
    public function userAgent(): string { return $this->server['HTTP_USER_AGENT'] ?? 'Unknown'; }
    public function file(string $key): ?array { return $this->files[$key] ?? null; }
}
