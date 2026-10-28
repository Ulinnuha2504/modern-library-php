<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= config('app.name') ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="app-wrapper">
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand">
                <div class="brand-icon-box">
                    <img src="<?= asset('images/logo.png') ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain;">
                </div>
                <div class="brand-text">
                    <h5>PERPUSTAKAAN</h5>
                    <small>Sistem Informasi</small>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-category">Menu Utama</li>
                <li class="menu-item"><a href="<?= url('dashboard') ?>" class="menu-link <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard') || ($_SERVER['REQUEST_URI'] ?? '') === '/' || str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/public/')) ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></li>
                <li class="menu-category">Katalog & Koleksi</li>
                <li class="menu-item"><a href="<?= url('buku') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/buku') ? 'active' : '' ?>"><i class="bi bi-journal-bookmark-fill"></i><span>Data Buku</span></a></li>
                <li class="menu-item"><a href="<?= url('kategori') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/kategori') ? 'active' : '' ?>"><i class="bi bi-tags-fill"></i><span>Kategori</span></a></li>
                <li class="menu-item"><a href="<?= url('rak') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/rak') ? 'active' : '' ?>"><i class="bi bi-bookshelf"></i><span>Lokasi Rak</span></a></li>
                <li class="menu-category">Keanggotaan</li>
                <li class="menu-item"><a href="<?= url('santri') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/santri') ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Data Santri</span></a></li>
                <?php if (is_role(['admin', 'petugas'])): ?>
                <li class="menu-category">Sirkulasi Layanan</li>
                <li class="menu-item"><a href="<?= url('transaksi/peminjaman') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'peminjaman') ? 'active' : '' ?>"><i class="bi bi-box-arrow-up-right"></i><span>Peminjaman Baru</span></a></li>
                <li class="menu-item"><a href="<?= url('transaksi/pengembalian') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'pengembalian') ? 'active' : '' ?>"><i class="bi bi-box-arrow-in-down-left"></i><span>Pengembalian & Denda</span></a></li>
                <li class="menu-item"><a href="<?= url('transaksi') ?>" class="menu-link <?= ($_SERVER['REQUEST_URI'] ?? '') === url('transaksi') ? 'active' : '' ?>"><i class="bi bi-clock-history"></i><span>Riwayat Transaksi</span></a></li>
                <?php endif; ?>
                <li class="menu-category">Statistik & Laporan</li>
                <li class="menu-item"><a href="<?= url('laporan') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/laporan') ? 'active' : '' ?>"><i class="bi bi-file-earmark-bar-graph-fill"></i><span>Laporan Perpustakaan</span></a></li>
                <?php if (is_role('admin')): ?>
                <li class="menu-category">Administrasi</li>
                <li class="menu-item"><a href="<?= url('users') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/users') ? 'active' : '' ?>"><i class="bi bi-person-gear"></i><span>Pengguna & Petugas</span></a></li>
                <li class="menu-item"><a href="<?= url('pengaturan') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/pengaturan') ? 'active' : '' ?>"><i class="bi bi-sliders"></i><span>Pengaturan Sistem</span></a></li>
                <?php endif; ?>
                <?php if (is_role(['admin', 'petugas'])): ?>
                <li class="menu-item"><a href="<?= url('logs') ?>" class="menu-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/logs') ? 'active' : '' ?>"><i class="bi bi-shield-check"></i><span>Log Aktivitas</span></a></li>
                <?php endif; ?>
            </ul>
            <div class="sidebar-footer">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-circle" style="width:34px;height:34px;font-size:0.85rem;"><?= strtoupper(substr(auth_user()['name'] ?? 'U', 0, 1)) ?></div>
                        <div style="line-height:1.2;">
                            <div class="text-white fw-semibold small text-truncate" style="max-width:130px;"><?= sanitize(auth_user()['name'] ?? 'Petugas') ?></div>
                            <small class="text-white-50" style="font-size:0.72rem;"><?= strtoupper(auth_user()['role'] ?? 'Petugas') ?></small>
                        </div>
                    </div>
                    <form action="<?= url('logout') ?>" method="POST" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-light border-0 text-white-50 p-1" title="Keluar Sistem" data-confirm="Keluar dari sistem?"><i class="bi bi-box-arrow-right fs-5"></i></button>
                    </form>
                </div>
            </div>
        </aside>
        <div class="app-main">
            <header class="app-navbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="nav-toggle-btn" id="sidebarToggle"><i class="bi bi-list"></i></button>
                    <div>
                        <h6 class="m-0 fw-bold text-dark"><?= isset($pageTitle) ? sanitize($pageTitle) : 'Sistem Informasi Perpustakaan' ?></h6>
                        <small class="text-muted" style="font-size:0.78rem;">Sistem Administrasi Perpustakaan Sekolah & Pesantren</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="badge-status aktif"><i class="bi bi-check-circle-fill"></i><span><?= strtoupper(auth_user()['role'] ?? 'USER') ?></span></div>
                    <div class="dropdown">
                        <div class="user-profile-badge" data-bs-toggle="dropdown">
                            <div class="avatar-circle"><?= strtoupper(substr(auth_user()['name'] ?? 'U', 0, 1)) ?></div>
                            <div class="d-none d-md-block text-start">
                                <span class="d-block fw-semibold text-dark small"><?= sanitize(auth_user()['name'] ?? 'User') ?></span>
                                <small class="text-muted" style="font-size:0.72rem;">@<?= sanitize(auth_user()['username'] ?? 'user') ?></small>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <li><span class="dropdown-item-text text-muted small"><?= sanitize(auth_user()['email'] ?? '') ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (is_role('admin')): ?><li><a class="dropdown-item" href="<?= url('pengaturan') ?>"><i class="bi bi-sliders me-2"></i>Pengaturan</a></li><?php endif; ?>
                            <li><form action="<?= url('logout') ?>" method="POST"><?= csrf_field() ?><button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button></form></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="toast-container-custom">
                <?php if (has_flash('success')): ?><div class="toast-custom"><i class="bi bi-check-circle-fill text-success fs-4"></i><div><div class="fw-bold text-dark small">Berhasil</div><div class="small text-muted"><?= sanitize(get_flash('success')) ?></div></div></div><?php endif; ?>
                <?php if (has_flash('error')): ?><div class="toast-custom error"><i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i><div><div class="fw-bold text-danger small">Perhatian</div><div class="small text-muted"><?= sanitize(get_flash('error')) ?></div></div></div><?php endif; ?>
            </div>
            <main class="app-content"><?= $content ?></main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
