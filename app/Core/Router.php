<?php
namespace App\Core;
class Router {
    protected array $routes = [];
    public function get(string $path, array|callable $handler, array $middlewares = []): self { $this->addRoute('GET', $path, $handler, $middlewares); return $this; }
    public function post(string $path, array|callable $handler, array $middlewares = []): self { $this->addRoute('POST', $path, $handler, $middlewares); return $this; }
    public function put(string $path, array|callable $handler, array $middlewares = []): self { $this->addRoute('PUT', $path, $handler, $middlewares); return $this; }
    public function delete(string $path, array|callable $handler, array $middlewares = []): self { $this->addRoute('DELETE', $path, $handler, $middlewares); return $this; }
    protected function addRoute(string $method, string $path, array|callable $handler, array $middlewares): void {
        $normalizedPath = '/' . trim($path, '/');
        if ($normalizedPath === '//') $normalizedPath = '/';
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $normalizedPath);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[] = ['method' => $method, 'path' => $normalizedPath, 'pattern' => $pattern, 'handler' => $handler, 'middlewares' => $middlewares];
    }
    public function dispatch(Request $request): void {
        $method = $request->method();
        $path = $request->path();
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle($request);
                }
                $handler = $route['handler'];
                if (is_callable($handler)) { call_user_func_array($handler, array_merge([$request], $params)); return; }
                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $action], array_merge([$request], $params));
                    return;
                }
            }
        }
        Response::setStatusCode(404);
        if ($request->isAjax()) {
            Response::json(['status' => 'error', 'message' => 'Halaman tidak ditemukan.'], 404);
        } else {
            echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:50px;"><h1 style="color:#166534">404</h1><p>Halaman tidak ditemukan.</p><a href="' . url('/') . '">Kembali ke Dashboard</a></body></html>';
        }
    }
}
