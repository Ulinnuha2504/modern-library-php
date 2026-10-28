<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div><a href="<?= url('laporan') ?>" class="text-success small">&larr; Kembali</a><h4 class="fw-bold m-0">Laporan Inventaris Buku</h4></div>
    <div><a href="<?= url('laporan/buku?status='.$status.'&kategori='.$kategoriId.'&format=print') ?>" target="_blank" class="btn btn-outline-secondary">Cetak</a> <a href="<?= url('laporan/buku?status='.$status.'&kategori='.$kategoriId.'&format=csv') ?>" class="btn btn-primary-green">Ekspor CSV</a></div>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>#</th><th>Kode</th><th>Judul</th><th>Kategori</th><th>Rak</th><th>Total</th><th>Ada</th><th>Pinjam</th><th>Rusak</th><th>Hilang</th></tr></thead>
        <tbody>
            <?php foreach ($books as $i => $b): ?>
            <tr><td><?= $i+1 ?></td><td class="font-monospace"><?= sanitize($b['kode_buku']) ?></td><td><strong><?= sanitize($b['judul']) ?></strong></td><td><?= sanitize($b['nama_kategori'] ?? '-') ?></td><td><?= sanitize($b['kode_rak'] ?? '-') ?></td><td><?= $b['jumlah_total'] ?></td><td class="text-success fw-bold"><?= $b['jumlah_tersedia'] ?></td><td class="text-warning fw-bold"><?= $b['jumlah_dipinjam'] ?></td><td class="text-danger fw-bold"><?= $b['jumlah_rusak'] ?></td><td class="text-secondary fw-bold"><?= $b['jumlah_hilang'] ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
