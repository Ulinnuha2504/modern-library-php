<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div><a href="<?= url('transaksi') ?>" class="text-success text-decoration-none small">&larr; Kembali</a><h4 class="fw-bold text-dark mt-1 mb-0">Bukti Transaksi <?= sanitize($transaksi['kode_transaksi']) ?></h4></div>
    <div><button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Cetak</button> <?php if (in_array($transaksi['status'], ['dipinjam', 'sebagian_dikembalikan'])): ?><a href="<?= url('transaksi/pengembalian?kode=' . urlencode($transaksi['kode_transaksi'])) ?>" class="btn btn-primary-green">Kembalikan Buku</a><?php endif; ?></div>
</div>
<div class="card-modern p-4">
    <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
        <div><h5 class="fw-bold text-dark m-0">PERPUSTAKAAN BAITUL HIKMAH</h5><small class="text-muted">Komplek Pesantren Darul Ulum</small></div>
        <div class="text-end"><span class="badge-status <?= $transaksi['status'] ?>"><?= strtoupper(str_replace('_', ' ', $transaksi['status'])) ?></span><div class="font-monospace fw-bold text-dark mt-1"><?= sanitize($transaksi['kode_transaksi']) ?></div></div>
    </div>
    <div class="row small mb-3">
        <div class="col-6"><strong>Santri:</strong> <?= sanitize($transaksi['nama_santri']) ?> (NIS: <?= sanitize($transaksi['nis']) ?>)<br>Kelas: <?= sanitize($transaksi['kelas']) ?> &bull; Asrama: <?= sanitize($transaksi['kamar']) ?></div>
        <div class="col-6 text-end">Tgl Pinjam: <?= format_tanggal($transaksi['tanggal_pinjam']) ?><br>Jatuh Tempo: <strong class="text-danger"><?= format_tanggal($transaksi['tanggal_jatuh_tempo']) ?></strong><br>Petugas: <?= sanitize($transaksi['nama_petugas']) ?></div>
    </div>
    <table class="table-modern mb-3">
        <thead><tr><th>#</th><th>Kode</th><th>Judul Buku</th><th>Kondisi Kembali</th><th>Denda</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($transaksi['items'] as $i => $it): ?>
            <tr><td><?= $i+1 ?></td><td class="font-monospace"><?= sanitize($it['kode_buku']) ?></td><td><?= sanitize($it['judul_buku']) ?></td><td><?= $it['kondisi_kembali'] ?: '-' ?></td><td><?= $it['denda'] > 0 ? format_rupiah($it['denda']) : '-' ?></td><td><span class="badge-status <?= $it['status'] ?>"><?= strtoupper($it['status']) ?></span></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="d-flex justify-content-between pt-2 border-top">
        <small class="text-muted">Catatan: <?= sanitize($transaksi['catatan'] ?: '-') ?></small>
        <div class="text-end"><strong>Total Denda:</strong> <span class="fs-5 fw-bold text-danger"><?= format_rupiah($transaksi['denda']) ?></span></div>
    </div>
</div>
