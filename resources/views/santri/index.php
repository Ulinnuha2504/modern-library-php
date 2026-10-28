<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-dark m-0">Data Santri</h4></div>
    <?php if (is_role(['admin', 'petugas'])): ?><a href="<?= url('santri/tambah') ?>" class="btn btn-primary-green">Tambah Santri</a><?php endif; ?>
</div>
<div class="card-modern mb-3">
    <form action="<?= url('santri') ?>" method="GET" class="row g-2">
        <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Cari NIS, Nama..." value="<?= sanitize($filters['q']) ?>"></div>
        <div class="col-md-3"><select name="kelas" class="form-select"><option value="">-- Semua Kelas --</option><?php foreach ($kelasList as $k): ?><option value="<?= $k ?>" <?= ($filters['kelas'] === $k)?'selected':'' ?>><?= sanitize($k) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">-- Status --</option><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary-green w-100">Filter</button></div>
    </form>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>NIS</th><th>Nama Santri</th><th>L/P</th><th>Kelas</th><th>Kamar</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach ($pagination['data'] as $s): ?>
            <tr>
                <td class="font-monospace fw-bold"><?= sanitize($s['nis']) ?></td><td><a href="<?= url('santri/detail/' . $s['id']) ?>" class="fw-bold text-dark text-decoration-none"><?= sanitize($s['nama']) ?></a></td>
                <td><?= $s['jenis_kelamin'] ?></td><td><?= sanitize($s['kelas']) ?></td><td><?= sanitize($s['kamar']) ?></td>
                <td><span class="badge-status <?= $s['status'] ?>"><?= strtoupper($s['status']) ?></span></td>
                <td><a href="<?= url('santri/detail/' . $s['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a> <?php if (is_role(['admin', 'petugas'])): ?><a href="<?= url('santri/edit/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a><?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
