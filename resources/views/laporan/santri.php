<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div><a href="<?= url('laporan') ?>" class="text-success small">&larr; Kembali</a><h4 class="fw-bold m-0">Laporan Santri</h4></div>
    <div><a href="<?= url('laporan/santri?filter='.$filter.'&kelas='.urlencode($kelas).'&format=print') ?>" target="_blank" class="btn btn-outline-secondary">Cetak</a> <a href="<?= url('laporan/santri?filter='.$filter.'&kelas='.urlencode($kelas).'&format=csv') ?>" class="btn btn-primary-green">Ekspor CSV</a></div>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>#</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Kamar</th><th>Pinjam Aktif</th><th>Total Denda</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($santriList as $i => $s): ?>
            <tr><td><?= $i+1 ?></td><td class="font-monospace"><?= sanitize($s['nis']) ?></td><td><strong><?= sanitize($s['nama']) ?></strong></td><td><?= sanitize($s['kelas']) ?></td><td><?= sanitize($s['kamar']) ?></td><td><?= $s['pinjam_aktif'] ?> Buku</td><td><?= format_rupiah($s['total_denda']) ?></td><td><?= strtoupper($s['status']) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
