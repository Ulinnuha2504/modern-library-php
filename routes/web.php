<?php
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\BukuController;
use App\Controllers\KategoriController;
use App\Controllers\RakController;
use App\Controllers\SantriController;
use App\Controllers\PeminjamanController;
use App\Controllers\PengembalianController;
use App\Controllers\LaporanController;
use App\Controllers\UserController;
use App\Controllers\SettingController;
use App\Controllers\LogController;

use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\PetugasMiddleware;
use App\Middleware\CsrfMiddleware;

// Auth
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Dashboard
$router->get('/', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

// Buku
$router->get('/buku', [BukuController::class, 'index'], [AuthMiddleware::class]);
$router->get('/buku/detail/{id}', [BukuController::class, 'detail'], [AuthMiddleware::class]);
$router->get('/buku/search-ajax', [BukuController::class, 'searchAjax'], [AuthMiddleware::class]);
$router->get('/buku/tambah', [BukuController::class, 'create'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->post('/buku/tambah', [BukuController::class, 'store'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->get('/buku/edit/{id}', [BukuController::class, 'edit'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->post('/buku/edit/{id}', [BukuController::class, 'update'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->post('/buku/toggle-status/{id}', [BukuController::class, 'toggleStatus'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);

// Kategori & Rak
$router->get('/kategori', [KategoriController::class, 'index'], [AuthMiddleware::class]);
$router->post('/kategori/tambah', [KategoriController::class, 'store'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->post('/kategori/edit/{id}', [KategoriController::class, 'update'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->post('/kategori/hapus/{id}', [KategoriController::class, 'delete'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);

$router->get('/rak', [RakController::class, 'index'], [AuthMiddleware::class]);
$router->post('/rak/tambah', [RakController::class, 'store'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->post('/rak/edit/{id}', [RakController::class, 'update'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->post('/rak/hapus/{id}', [RakController::class, 'delete'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);

// Santri
$router->get('/santri', [SantriController::class, 'index'], [AuthMiddleware::class]);
$router->get('/santri/detail/{id}', [SantriController::class, 'detail'], [AuthMiddleware::class]);
$router->get('/santri/search-ajax', [SantriController::class, 'searchAjax'], [AuthMiddleware::class]);
$router->get('/santri/tambah', [SantriController::class, 'create'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->post('/santri/tambah', [SantriController::class, 'store'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->get('/santri/edit/{id}', [SantriController::class, 'edit'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->post('/santri/edit/{id}', [SantriController::class, 'update'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);

// Sirkulasi
$router->get('/transaksi', [PeminjamanController::class, 'index'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->get('/transaksi/peminjaman', [PeminjamanController::class, 'create'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->post('/transaksi/peminjaman', [PeminjamanController::class, 'store'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);
$router->get('/transaksi/detail/{id}', [PeminjamanController::class, 'detail'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->get('/transaksi/pengembalian', [PengembalianController::class, 'index'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->get('/transaksi/pengembalian/search-ajax', [PengembalianController::class, 'searchAjax'], [AuthMiddleware::class, PetugasMiddleware::class]);
$router->post('/transaksi/pengembalian/proses', [PengembalianController::class, 'process'], [AuthMiddleware::class, PetugasMiddleware::class, CsrfMiddleware::class]);

// Laporan
$router->get('/laporan', [LaporanController::class, 'index'], [AuthMiddleware::class]);
$router->get('/laporan/buku', [LaporanController::class, 'buku'], [AuthMiddleware::class]);
$router->get('/laporan/santri', [LaporanController::class, 'santri'], [AuthMiddleware::class]);
$router->get('/laporan/peminjaman', [LaporanController::class, 'peminjaman'], [AuthMiddleware::class]);
$router->get('/laporan/pengembalian', [LaporanController::class, 'pengembalian'], [AuthMiddleware::class]);

// Admin
$router->get('/users', [UserController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/users/tambah', [UserController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
$router->post('/users/edit/{id}', [UserController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
$router->post('/users/toggle-status/{id}', [UserController::class, 'toggleStatus'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
$router->get('/pengaturan', [SettingController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/pengaturan', [SettingController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
$router->get('/logs', [LogController::class, 'index'], [AuthMiddleware::class, PetugasMiddleware::class]);
