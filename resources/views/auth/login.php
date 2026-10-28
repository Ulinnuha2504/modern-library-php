<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Masuk — <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: system-ui, sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 40px rgba(22,101,52,0.08); width: 100%; max-width: 440px; padding: 40px 36px; border: 1px solid #e2e8f0; }
        .brand-icon { width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #166534, #047857); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px; }
        .btn-login { background: linear-gradient(135deg, #166534, #047857); color: #fff; border: none; border-radius: 10px; padding: 13px; font-weight: 700; width: 100%; }
        .btn-login:hover { background: linear-gradient(135deg, #14532d, #065f46); color:#fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon"><i class="bi bi-book-half"></i></div>
            <h4 class="fw-bold m-0" style="color: #166534;">PERPUSTAKAAN</h4>
            <p class="text-muted small mb-0">Sistem Informasi Perpustakaan Pesantren</p>
        </div>
        <?php if (has_flash('error')): ?><div class="alert alert-danger py-2 px-3 small rounded-3 mb-3"><?= sanitize(get_flash('error')) ?></div><?php endif; ?>
        <?php if (has_flash('success')): ?><div class="alert alert-success py-2 px-3 small rounded-3 mb-3"><?= sanitize(get_flash('success')) ?></div><?php endif; ?>
        <form action="<?= url('login') ?>" method="POST" autocomplete="off">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Nama Pengguna / Surel</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" name="username" value="<?= sanitize(old('username', 'admin')) ?>" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" value="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-login mb-3"><i class="bi bi-box-arrow-in-right me-2"></i>MASUK KE SISTEM</button>
        </form>
        <div class="bg-light p-3 rounded-3 text-center border">
            <small class="text-muted d-block fw-semibold mb-1">Kredensial Bawaan:</small>
            <small class="text-secondary d-block font-monospace">Admin: <strong>admin</strong> | Password: <strong>password</strong></small>
            <small class="text-secondary d-block font-monospace">Petugas: <strong>petugas</strong> | Password: <strong>password</strong></small>
        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const pwd = document.getElementById('password');
            pwd.type = (pwd.type === 'password') ? 'text' : 'password';
        });
    </script>
</body>
</html>
