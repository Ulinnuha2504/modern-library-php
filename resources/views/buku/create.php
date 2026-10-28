<div class="mb-4">
    <a href="<?= url('buku') ?>" class="text-success text-decoration-none small fw-semibold">&larr; Kembali ke Katalog</a>
    <h4 class="fw-bold text-dark mt-2 mb-0">Tambah Judul Buku Baru</h4>
</div>
<div class="card-modern">
    <form action="<?= url('buku/tambah') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-semibold">Kode Buku</label><input type="text" name="kode_buku" class="form-control font-monospace fw-bold" value="<?= sanitize(old('kode_buku', $generatedKode)) ?>" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">ISBN</label><input type="text" name="isbn" class="form-control" value="<?= sanitize(old('isbn')) ?>"></div>
            <div class="col-12"><label class="form-label small fw-semibold">Judul Buku</label><input type="text" name="judul" class="form-control" value="<?= sanitize(old('judul')) ?>" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Penulis</label><input type="text" name="penulis" class="form-control" value="<?= sanitize(old('penulis')) ?>" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Penerbit</label><input type="text" name="penerbit" class="form-control" value="<?= sanitize(old('penerbit')) ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Tahun</label><input type="number" name="tahun_terbit" class="form-control" value="<?= date('Y') ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Kategori</label><select name="kategori_id" class="form-select" required><?php foreach ($kategoriList as $k): ?><option value="<?= $k['id'] ?>"><?= sanitize($k['nama']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Rak</label><select name="rak_id" class="form-select" required><?php foreach ($rakList as $r): ?><option value="<?= $r['id'] ?>"><?= sanitize($r['kode_rak']) ?> - <?= sanitize($r['nama_rak']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Jumlah Eksemplar Fisik</label><input type="number" name="jumlah_total" class="form-control" value="1" min="1" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Cover Buku</label><input type="file" name="cover" class="form-control" accept="image/*"></div>
            <div class="col-12"><label class="form-label small fw-semibold">Sinopsis</label><textarea name="deskripsi" class="form-control" rows="3"><?= sanitize(old('deskripsi')) ?></textarea></div>
            <div class="col-12 mt-3 pt-3 border-top"><button type="submit" class="btn btn-primary-green"><i class="bi bi-save me-1"></i> Simpan Buku</button> <a href="<?= url('buku') ?>" class="btn btn-outline-secondary">Batal</a></div>
        </div>
    </form>
</div>
