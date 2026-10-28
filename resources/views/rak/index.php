<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-dark m-0">Lokasi Fisik Rak Buku</h4></div>
    <?php if (is_role(['admin', 'petugas'])): ?><button class="btn btn-primary-green" data-bs-toggle="modal" data-bs-target="#mRak">Tambah Rak</button><?php endif; ?>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>Kode</th><th>Nama Rak</th><th>Lokasi</th><th>Total Buku</th><?php if (is_role(['admin', 'petugas'])): ?><th>Aksi</th><?php endif; ?></tr></thead>
        <tbody>
            <?php foreach ($rakList as $r): ?>
            <tr>
                <td class="font-monospace fw-bold"><?= sanitize($r['kode_rak']) ?></td><td><?= sanitize($r['nama_rak']) ?></td><td><?= sanitize($r['lokasi']) ?></td><td><?= $r['total_buku'] ?> Judul</td>
                <?php if (is_role(['admin', 'petugas'])): ?><td><form action="<?= url('rak/hapus/' . $r['id']) ?>" method="POST" class="d-inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Hapus rak?"><i class="bi bi-trash"></i></button></form></td><?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="mRak" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="<?= url('rak/tambah') ?>" method="POST"><?= csrf_field() ?><div class="modal-header"><h6 class="modal-title fw-bold">Tambah Rak</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="small fw-semibold">Kode Rak</label><input type="text" name="kode_rak" class="form-control" required></div><div class="mb-3"><label class="small fw-semibold">Nama Rak</label><input type="text" name="nama_rak" class="form-control" required></div><div class="mb-3"><label class="small fw-semibold">Lokasi</label><input type="text" name="lokasi" class="form-control" required></div></div><div class="modal-footer"><button type="submit" class="btn btn-primary-green btn-sm">Simpan</button></div></form></div></div></div>
