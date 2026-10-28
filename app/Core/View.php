<?php
namespace App\Core;
class View {
    public static function render(string $viewPath, array $data = [], string|bool $layout = 'main'): void {
        $baseViewDir = __DIR__ . '/../../resources/views/';
        $viewFile = $baseViewDir . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) die("View template [{$viewPath}] tidak ditemukan pada: " . htmlspecialchars($viewFile));
        extract($data);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        if (is_string($layout) && $layout !== '') {
            $layoutFile = $baseViewDir . 'layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) { require $layoutFile; return; }
        }
        echo $content;
    }
}
