<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('santri') ?>" class="text-success text-decoration-none small">&larr; Kembali ke Santri</a><h4 class="fw-bold text-dark mt-1 mb-0"><?= sanitize($santri['nama']) ?></h4></div>
    <a href="<?= url('transaksi/peminjaman') ?>" class="btn btn-primary-green">Pinjamkan Buku</a>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-modern text-center">
            <div class="avatar-circle mx-auto mb-2" style="width:60px;height:60px;font-size:1.5rem;"><?= strtoupper(substr($santri['nama'],0,1)) ?></div>
            <h5 class="fw-bold"><?= sanitize($santri['nama']) ?></h5>
            <p class="text-muted small">NIS: <?= sanitize($santri['nis']) ?> &bull; <?= sanitize($santri['kelas']) ?></p>
            <hr>
            <div class="text-start small">
                <div><strong>Kamar:</strong> <?= sanitize($santri['kamar']) ?></div>
                <div><strong>Wali:</strong> <?= sanitize($santri['nama_wali'] ?: '-') ?></div>
                <div><strong>No HP:</strong> <?= sanitize($santri['no_hp'] ?: '-') ?></div>
                <div><strong>Status:</strong> <span class="badge-status <?= $santri['status'] ?>"><?= strtoupper($santri['status']) ?></span></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-modern mb-4">
            <div class="d-flex justify-content-around text-center">
                <div><small class="text-muted d-block">SEDANG DIPINJAM</small><span class="fs-4 fw-bold text-warning"><?= $activeLoansCount ?> Buku</span></div>
                <div class="border-start"></div>
                <div><small class="text-muted d-block">TOTAL DENDA</small><span class="fs-4 fw-bold text-danger"><?= format_rupiah($totalDenda) ?></span></div>
            </div>
        </div>
        <div class="card-modern p-0 overflow-hidden">
            <div class="p-3 border-bottom bg-light"><h6 class="fw-bold m-0">Riwayat Peminjaman</h6></div>
            <table class="table-modern">
                <thead><tr><th>Kode TRX</th><th>Buku</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Denda</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if (empty($history)): ?><tr><td colspan="6" class="text-center py-3 text-muted">Belum ada riwayat peminjaman.</td></tr><?php else: ?>
                    <?php foreach ($history as $h): ?>
                    <tr><td><?= sanitize($h['kode_transaksi']) ?></td><td><?= sanitize($h['judul_buku']) ?></td><td><?= format_tanggal($h['tanggal_pinjam']) ?></td><td><?= format_tanggal($h['tanggal_jatuh_tempo']) ?></td><td><?= $h['denda'] > 0 ? format_rupiah($h['denda']) : '-' ?></td><td><span class="badge-status <?= $h['status_item'] ?>"><?= strtoupper($h['status_item']) ?></span></td></tr>
                    <?php endforeach; ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
