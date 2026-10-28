<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div><h4 class="fw-bold text-dark m-0">Katalog Data Buku</h4><p class="text-muted small mb-0">Kelola informasi judul, penulis, penempatan rak, dan stok eksemplar buku.</p></div>
    <?php if (is_role(['admin', 'petugas'])): ?><a href="<?= url('buku/tambah') ?>" class="btn btn-primary-green"><i class="bi bi-plus-circle me-1"></i> Tambah Judul Buku</a><?php endif; ?>
</div>
<div class="card-modern mb-4">
    <form action="<?= url('buku') ?>" method="GET" class="row g-2">
        <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Cari Kode, ISBN, Judul, Penulis..." value="<?= sanitize($filters['search']) ?>"></div>
        <div class="col-md-3"><select name="kategori" class="form-select"><option value="">-- Semua Kategori --</option><?php foreach ($kategoriList as $k): ?><option value="<?= $k['id'] ?>" <?= ($filters['kategori_id'] == $k['id']) ? 'selected' : '' ?>><?= sanitize($k['nama']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="rak" class="form-select"><option value="">-- Semua Lokasi Rak --</option><?php foreach ($rakList as $r): ?><option value="<?= $r['id'] ?>" <?= ($filters['rak_id'] == $r['id']) ? 'selected' : '' ?>><?= sanitize($r['kode_rak']) ?> - <?= sanitize($r['nama_rak']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2 d-flex gap-2"><button type="submit" class="btn btn-primary-green w-100 justify-content-center">Filter</button><a href="<?= url('buku') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </form>
</div>
<div class="card-modern p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table-modern">
            <thead><tr><th style="width:60px;">Cover</th><th>Kode / ISBN</th><th>Judul Buku & Penulis</th><th>Kategori</th><th>Rak</th><th class="text-center">Total</th><th class="text-center">Tersedia</th><th class="text-center">Dipinjam</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($pagination['data'])): ?><tr><td colspan="10" class="text-center py-4 text-muted">Belum ada data buku.</td></tr><?php else: ?>
                <?php foreach ($pagination['data'] as $b): ?>
                <tr>
                    <td><?php if ($b['cover']): ?><img src="<?= url('uploads/covers/' . $b['cover']) ?>" style="width:40px;height:55px;object-fit:cover;" class="rounded border"><?php else: ?><div class="rounded bg-light border d-flex align-items-center justify-content-center" style="width:40px;height:55px;"><i class="bi bi-book text-muted"></i></div><?php endif; ?></td>
                    <td><span class="fw-bold font-monospace text-dark d-block"><?= sanitize($b['kode_buku']) ?></span><small class="text-muted"><?= sanitize($b['isbn'] ?: '-') ?></small></td>
                    <td><a href="<?= url('buku/detail/' . $b['id']) ?>" class="fw-bold text-dark text-decoration-none d-block"><?= sanitize($b['judul']) ?></a><small class="text-muted"><?= sanitize($b['penulis']) ?> &bull; <?= $b['tahun_terbit'] ?></small></td>
                    <td><span class="badge bg-light text-dark border"><?= sanitize($b['nama_kategori'] ?? '-') ?></span></td>
                    <td><small class="text-muted font-monospace"><?= sanitize($b['kode_rak'] ?? '-') ?></small></td>
                    <td class="text-center fw-bold"><?= $b['jumlah_total'] ?></td>
                    <td class="text-center fw-bold text-success"><?= $b['jumlah_tersedia'] ?></td>
                    <td class="text-center fw-bold text-warning"><?= $b['jumlah_dipinjam'] ?></td>
                    <td><span class="badge-status <?= $b['status'] ?>"><?= strtoupper($b['status']) ?></span></td>
                    <td class="text-center"><a href="<?= url('buku/detail/' . $b['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a> <?php if (is_role(['admin', 'petugas'])): ?><a href="<?= url('buku/edit/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a><?php endif; ?></td>
                </tr>
                <?php endforeach; ?><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
