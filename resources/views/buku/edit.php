<div class="mb-4">
    <a href="<?= url('buku/detail/' . $buku['id']) ?>" class="text-success text-decoration-none small fw-semibold">&larr; Kembali ke Detail</a>
    <h4 class="fw-bold text-dark mt-2 mb-0">Ubah Data Buku</h4>
</div>
<div class="card-modern">
    <form action="<?= url('buku/edit/' . $buku['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-semibold">Kode Buku</label><input type="text" class="form-control font-monospace fw-bold bg-light" value="<?= sanitize($buku['kode_buku']) ?>" readonly></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">ISBN</label><input type="text" name="isbn" class="form-control" value="<?= sanitize(old('isbn', $buku['isbn'])) ?>"></div>
            <div class="col-12"><label class="form-label small fw-semibold">Judul</label><input type="text" name="judul" class="form-control" value="<?= sanitize(old('judul', $buku['judul'])) ?>" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Penulis</label><input type="text" name="penulis" class="form-control" value="<?= sanitize(old('penulis', $buku['penulis'])) ?>" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Penerbit</label><input type="text" name="penerbit" class="form-control" value="<?= sanitize(old('penerbit', $buku['penerbit'])) ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Tahun</label><input type="number" name="tahun_terbit" class="form-control" value="<?= $buku['tahun_terbit'] ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Kategori</label><select name="kategori_id" class="form-select"><?php foreach ($kategoriList as $k): ?><option value="<?= $k['id'] ?>" <?= ($buku['kategori_id'] == $k['id']) ? 'selected' : '' ?>><?= sanitize($k['nama']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Rak</label><select name="rak_id" class="form-select"><?php foreach ($rakList as $r): ?><option value="<?= $r['id'] ?>" <?= ($buku['rak_id'] == $r['id']) ? 'selected' : '' ?>><?= sanitize($r['kode_rak']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Tambah Stok Eksemplar</label><input type="number" name="tambah_stok" class="form-control" value="0" min="0"></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Status</label><select name="status" class="form-select"><option value="aktif" <?= ($buku['status'] === 'aktif') ? 'selected' : '' ?>>Aktif</option><option value="nonaktif" <?= ($buku['status'] === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Cover Baru</label><input type="file" name="cover" class="form-control" accept="image/*"></div>
            <div class="col-12"><label class="form-label small fw-semibold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3"><?= sanitize(old('deskripsi', $buku['deskripsi'])) ?></textarea></div>
            <div class="col-12 mt-3 pt-3 border-top"><button type="submit" class="btn btn-primary-green">Perbarui Data</button> <a href="<?= url('buku/detail/' . $buku['id']) ?>" class="btn btn-outline-secondary">Batal</a></div>
        </div>
    </form>
</div>
