<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-dark m-0">Kategori Subjek Buku</h4></div>
    <?php if (is_role(['admin', 'petugas'])): ?><button class="btn btn-primary-green" data-bs-toggle="modal" data-bs-target="#mKat">Tambah Kategori</button><?php endif; ?>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>#</th><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Buku</th><th>Status</th><?php if (is_role(['admin', 'petugas'])): ?><th>Aksi</th><?php endif; ?></tr></thead>
        <tbody>
            <?php foreach ($kategoriList as $i => $k): ?>
            <tr>
                <td><?= $i + 1 ?></td><td class="fw-bold"><?= sanitize($k['nama']) ?></td><td><?= sanitize($k['deskripsi'] ?: '-') ?></td><td><?= $k['total_buku'] ?> Judul</td>
                <td><span class="badge-status <?= $k['status'] ?>"><?= strtoupper($k['status']) ?></span></td>
                <?php if (is_role(['admin', 'petugas'])): ?>
                <td><form action="<?= url('kategori/hapus/' . $k['id']) ?>" method="POST" class="d-inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Hapus kategori?"><i class="bi bi-trash"></i></button></form></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="mKat" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="<?= url('kategori/tambah') ?>" method="POST"><?= csrf_field() ?><div class="modal-header"><h6 class="modal-title fw-bold">Tambah Kategori</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="small fw-semibold">Nama Kategori</label><input type="text" name="nama" class="form-control" required></div><div class="mb-3"><label class="small fw-semibold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div></div><div class="modal-footer"><button type="submit" class="btn btn-primary-green btn-sm">Simpan</button></div></form></div></div></div>
