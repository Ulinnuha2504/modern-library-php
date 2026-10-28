<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-dark m-0">Manajemen Pengguna & Petugas</h4></div>
    <button class="btn btn-primary-green" data-bs-toggle="modal" data-bs-target="#mUser">Tambah Pengguna</button>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach ($users as $i => $u): ?>
            <tr>
                <td><?= $i+1 ?></td><td><strong><?= sanitize($u['name']) ?></strong></td><td class="font-monospace">@<?= sanitize($u['username']) ?></td><td><?= sanitize($u['email']) ?></td>
                <td><span class="badge bg-light text-dark border"><?= strtoupper($u['role']) ?></span></td><td><span class="badge-status <?= $u['status'] ?>"><?= strtoupper($u['status']) ?></span></td>
                <td><?php if ($u['id'] !== auth_user()['id']): ?><form action="<?= url('users/toggle-status/' . $u['id']) ?>" method="POST" class="d-inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-warning" data-confirm="Ubah status akun?"><i class="bi bi-power"></i></button></form><?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="mUser" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="<?= url('users/tambah') ?>" method="POST"><?= csrf_field() ?><div class="modal-header"><h6 class="modal-title fw-bold">Tambah Pengguna</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="small fw-semibold">Nama Lengkap</label><input type="text" name="name" class="form-control" required></div><div class="mb-3"><label class="small fw-semibold">Username</label><input type="text" name="username" class="form-control" required></div><div class="mb-3"><label class="small fw-semibold">Email</label><input type="email" name="email" class="form-control" required></div><div class="mb-3"><label class="small fw-semibold">Role</label><select name="role" class="form-select"><option value="petugas">Petugas</option><option value="admin">Administrator</option><option value="viewer">Viewer</option></select></div><div class="mb-3"><label class="small fw-semibold">Kata Sandi</label><input type="password" name="password" class="form-control" required></div></div><div class="modal-footer"><button type="submit" class="btn btn-primary-green btn-sm">Daftarkan</button></div></form></div></div></div>
