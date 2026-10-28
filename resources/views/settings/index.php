<div class="mb-4"><h4 class="fw-bold text-dark m-0">Pengaturan Sistem</h4></div>
<div class="card-modern"><form action="<?= url('pengaturan') ?>" method="POST"><?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-4"><label class="small fw-semibold">Denda per Hari</label><div class="input-group"><span class="input-group-text">Rp</span><input type="number" name="denda_per_hari" class="form-control" value="<?= sanitize($settings['denda_per_hari'] ?? '1000') ?>" required></div></div>
    <div class="col-md-4"><label class="small fw-semibold">Durasi Pinjam</label><div class="input-group"><input type="number" name="lama_peminjaman" class="form-control" value="<?= sanitize($settings['lama_peminjaman'] ?? '7') ?>" required><span class="input-group-text">Hari</span></div></div>
    <div class="col-md-4"><label class="small fw-semibold">Maksimal Pinjam</label><div class="input-group"><input type="number" name="maksimal_pinjaman" class="form-control" value="<?= sanitize($settings['maksimal_pinjaman'] ?? '3') ?>" required><span class="input-group-text">Buku</span></div></div>
    <div class="col-md-6"><label class="small fw-semibold">Denda Rusak Ringan</label><input type="number" name="denda_rusak_ringan" class="form-control" value="<?= sanitize($settings['denda_rusak_ringan'] ?? '5000') ?>"></div>
    <div class="col-md-6"><label class="small fw-semibold">Denda Rusak Berat</label><input type="number" name="denda_rusak_berat" class="form-control" value="<?= sanitize($settings['denda_rusak_berat'] ?? '20000') ?>"></div>
    <div class="col-12"><label class="small fw-semibold">Nama Perpustakaan</label><input type="text" name="nama_perpustakaan" class="form-control" value="<?= sanitize($settings['nama_perpustakaan'] ?? 'Perpustakaan Baitul Hikmah') ?>" required></div>
    <div class="col-12"><label class="small fw-semibold">Alamat</label><textarea name="alamat_perpustakaan" class="form-control" rows="2"><?= sanitize($settings['alamat_perpustakaan'] ?? '') ?></textarea></div>
    <div class="col-12 mt-3 pt-3 border-top"><button type="submit" class="btn btn-primary-green">Simpan Pengaturan</button></div>
</div></form></div>
