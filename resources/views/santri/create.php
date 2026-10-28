<div class="mb-4"><a href="<?= url('santri') ?>" class="text-success text-decoration-none small">&larr; Kembali</a><h4 class="fw-bold text-dark mt-1 mb-0">Tambah Santri</h4></div>
<div class="card-modern"><form action="<?= url('santri/tambah') ?>" method="POST"><?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-6"><label class="small fw-semibold">NIS</label><input type="text" name="nis" class="form-control" required></div>
    <div class="col-md-6"><label class="small fw-semibold">NISN</label><input type="text" name="nisn" class="form-control"></div>
    <div class="col-md-8"><label class="small fw-semibold">Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
    <div class="col-md-4"><label class="small fw-semibold">L/P</label><select name="jenis_kelamin" class="form-select"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
    <div class="col-md-4"><label class="small fw-semibold">Kelas</label><input type="text" name="kelas" class="form-control" required></div>
    <div class="col-md-4"><label class="small fw-semibold">Kamar</label><input type="text" name="kamar" class="form-control" required></div>
    <div class="col-md-4"><label class="small fw-semibold">Tahun Masuk</label><input type="number" name="tahun_masuk" class="form-control" value="<?= date('Y') ?>" required></div>
    <div class="col-md-6"><label class="small fw-semibold">Nama Wali</label><input type="text" name="nama_wali" class="form-control"></div>
    <div class="col-md-6"><label class="small fw-semibold">No HP/WA</label><input type="text" name="no_hp" class="form-control"></div>
    <div class="col-12"><label class="small fw-semibold">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-primary-green">Simpan Santri</button></div>
</div></form></div>
