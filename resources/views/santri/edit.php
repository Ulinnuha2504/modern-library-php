<div class="mb-4"><a href="<?= url('santri/detail/' . $santri['id']) ?>" class="text-success text-decoration-none small">&larr; Kembali</a><h4 class="fw-bold text-dark mt-1 mb-0">Ubah Santri</h4></div>
<div class="card-modern"><form action="<?= url('santri/edit/' . $santri['id']) ?>" method="POST"><?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-6"><label class="small fw-semibold">NIS</label><input type="text" class="form-control bg-light" value="<?= sanitize($santri['nis']) ?>" readonly></div>
    <div class="col-md-6"><label class="small fw-semibold">Nama</label><input type="text" name="nama" class="form-control" value="<?= sanitize($santri['nama']) ?>" required></div>
    <div class="col-md-4"><label class="small fw-semibold">Kelas</label><input type="text" name="kelas" class="form-control" value="<?= sanitize($santri['kelas']) ?>" required></div>
    <div class="col-md-4"><label class="small fw-semibold">Kamar</label><input type="text" name="kamar" class="form-control" value="<?= sanitize($santri['kamar']) ?>" required></div>
    <div class="col-md-4"><label class="small fw-semibold">Status</label><select name="status" class="form-select"><option value="aktif" <?= ($santri['status']==='aktif')?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= ($santri['status']==='nonaktif')?'selected':'' ?>>Nonaktif</option></select></div>
    <div class="col-12"><button type="submit" class="btn btn-primary-green">Perbarui Santri</button></div>
</div></form></div>
