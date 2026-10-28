<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div><a href="<?= url('laporan') ?>" class="text-success small">&larr; Kembali</a><h4 class="fw-bold m-0">Laporan Pengembalian & Denda</h4></div>
    <div><a href="<?= url('laporan/pengembalian?tgl_awal='.$tglAwal.'&tgl_akhir='.$tglAkhir.'&format=print') ?>" target="_blank" class="btn btn-outline-secondary">Cetak</a> <a href="<?= url('laporan/pengembalian?tgl_awal='.$tglAwal.'&tgl_akhir='.$tglAkhir.'&format=csv') ?>" class="btn btn-primary-green">Ekspor CSV</a></div>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>#</th><th>Kode TRX</th><th>Santri</th><th>Buku</th><th>Tgl Kembali</th><th>Kondisi</th><th>Denda</th><th>Petugas</th></tr></thead>
        <tbody>
            <?php $td = 0; foreach ($kembaliList as $i => $k): $td += $k['denda']; ?>
            <tr><td><?= $i+1 ?></td><td class="font-monospace"><?= sanitize($k['kode_transaksi']) ?></td><td><?= sanitize($k['nama_santri']) ?></td><td><?= sanitize($k['judul_buku']) ?></td><td><?= format_tanggal($k['tanggal_kembali']) ?></td><td><?= $k['kondisi_kembali'] ?></td><td class="text-danger fw-bold"><?= format_rupiah($k['denda']) ?></td><td><?= sanitize($k['nama_petugas']) ?></td></tr>
            <?php endforeach; ?>
            <tr class="table-light fw-bold"><td colspan="6" class="text-end">TOTAL KAS DENDA:</td><td class="text-danger"><?= format_rupiah($td) ?></td><td></td></tr>
        </tbody>
    </table>
</div>
