<?php
declare(strict_types=1);
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/Autoloader.php';
$autoloader = new \App\Core\Autoloader(BASE_PATH . '/app');
$autoloader->register();
require_once BASE_PATH . '/app/Helpers/functions.php';
\App\Core\Env::load(BASE_PATH . '/.env');
if (config('app.debug', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
\App\Core\Session::start();
$router = new \App\Core\Router();
$request = new \App\Core\Request();
require_once BASE_PATH . '/routes/web.php';
$router->dispatch($request);
