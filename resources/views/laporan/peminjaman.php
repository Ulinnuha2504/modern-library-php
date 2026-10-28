<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div><a href="<?= url('laporan') ?>" class="text-success small">&larr; Kembali</a><h4 class="fw-bold m-0">Laporan Peminjaman</h4></div>
    <div><a href="<?= url('laporan/peminjaman?tgl_awal='.$tglAwal.'&tgl_akhir='.$tglAkhir.'&status='.$status.'&format=print') ?>" target="_blank" class="btn btn-outline-secondary">Cetak</a> <a href="<?= url('laporan/peminjaman?tgl_awal='.$tglAwal.'&tgl_akhir='.$tglAkhir.'&status='.$status.'&format=csv') ?>" class="btn btn-primary-green">Ekspor CSV</a></div>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>#</th><th>Kode TRX</th><th>Tgl Pinjam</th><th>Santri</th><th>Daftar Buku</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($peminjamanList as $i => $p): ?>
            <tr><td><?= $i+1 ?></td><td class="font-monospace fw-bold"><?= sanitize($p['kode_transaksi']) ?></td><td><?= format_tanggal($p['tanggal_pinjam']) ?></td><td><?= sanitize($p['nama_santri']) ?> (<?= sanitize($p['kelas']) ?>)</td><td><small><?= sanitize($p['daftar_buku']) ?></small></td><td><?= $p['jumlah_buku'] ?></td><td><?= strtoupper(str_replace('_', ' ', $p['status'])) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
