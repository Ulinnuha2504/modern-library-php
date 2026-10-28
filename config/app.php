<?php
use App\Core\Env;
return [
    'name' => Env::get('APP_NAME', 'Sistem Informasi Perpustakaan'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::get('APP_DEBUG', false),
    'url' => rtrim(Env::get('APP_URL', 'http://localhost'), '/'),
    'timezone' => Env::get('APP_TIMEZONE', 'Asia/Jakarta'),
    'session_lifetime' => (int) Env::get('SESSION_LIFETIME', 120),
];
