<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card-modern bg-white p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="border-left: 6px solid #166534 !important;">
            <div>
                <h4 class="fw-bold text-dark m-0">Selamat Datang, <?= sanitize(auth_user()['name'] ?? 'Petugas') ?>! 👋</h4>
                <p class="text-muted small mb-0 mt-1">Sistem Informasi Perpustakaan siap melayani operasional sirkulasi dan literasi santri.</p>
            </div>
            <div class="d-flex gap-2">
                <?php if (is_role(['admin', 'petugas'])): ?>
                <a href="<?= url('transaksi/peminjaman') ?>" class="btn btn-primary-green"><i class="bi bi-plus-circle"></i> Peminjaman Baru</a>
                <a href="<?= url('transaksi/pengembalian') ?>" class="btn btn-outline-green"><i class="bi bi-box-arrow-in-down-left"></i> Pengembalian Buku</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-card-inner"><div><div class="stat-label">Pinjam Hari Ini</div><div class="stat-value text-success"><?= number_format($statTrx['pinjam_hari_ini'] ?? 0) ?></div><small class="text-muted">Transaksi baru</small></div><div class="stat-icon green"><i class="bi bi-arrow-up-right-circle-fill"></i></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-card-inner"><div><div class="stat-label">Kembali Hari Ini</div><div class="stat-value text-primary"><?= number_format($statTrx['kembali_hari_ini'] ?? 0) ?></div><small class="text-muted">Eksemplar diterima</small></div><div class="stat-icon emerald"><i class="bi bi-arrow-down-left-circle-fill"></i></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-card-inner"><div><div class="stat-label">Sedang Dipinjam</div><div class="stat-value text-warning"><?= number_format($statTrx['sedang_dipinjam'] ?? 0) ?></div><small class="text-muted">Di tangan santri</small></div><div class="stat-icon amber"><i class="bi bi-journal-check"></i></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-card-inner"><div><div class="stat-label">Lewat Tempo</div><div class="stat-value text-danger"><?= number_format($statTrx['transaksi_terlambat'] ?? 0) ?></div><small class="text-danger">Perlu penagihan</small></div><div class="stat-icon rose"><i class="bi bi-exclamation-octagon-fill"></i></div></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card-modern h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-book me-2 text-success"></i>Kondisi Inventaris Buku</h6>
                <a href="<?= url('buku') ?>" class="text-success small fw-semibold text-decoration-none">Kelola Buku &rarr;</a>
            </div>
            <div class="row g-2 text-center">
                <div class="col-4 col-sm-2"><div class="p-2 border rounded-3 bg-light"><small class="text-muted d-block" style="font-size:0.7rem;">JUDUL</small><span class="fs-5 fw-bold text-dark"><?= number_format($statBuku['total_judul']) ?></span></div></div>
                <div class="col-4 col-sm-2"><div class="p-2 border rounded-3 bg-light"><small class="text-muted d-block" style="font-size:0.7rem;">EKSEMPLAR</small><span class="fs-5 fw-bold text-dark"><?= number_format($statBuku['total_eksemplar']) ?></span></div></div>
                <div class="col-4 col-sm-2"><div class="p-2 border rounded-3 bg-light"><small class="text-success d-block" style="font-size:0.7rem;">TERSEDIA</small><span class="fs-5 fw-bold text-success"><?= number_format($statBuku['buku_tersedia']) ?></span></div></div>
                <div class="col-4 col-sm-2"><div class="p-2 border rounded-3 bg-light"><small class="text-warning d-block" style="font-size:0.7rem;">DIPINJAM</small><span class="fs-5 fw-bold text-warning"><?= number_format($statBuku['buku_dipinjam']) ?></span></div></div>
                <div class="col-4 col-sm-2"><div class="p-2 border rounded-3 bg-light"><small class="text-danger d-block" style="font-size:0.7rem;">RUSAK</small><span class="fs-5 fw-bold text-danger"><?= number_format($statBuku['buku_rusak']) ?></span></div></div>
                <div class="col-4 col-sm-2"><div class="p-2 border rounded-3 bg-light"><small class="text-secondary d-block" style="font-size:0.7rem;">HILANG</small><span class="fs-5 fw-bold text-secondary"><?= number_format($statBuku['buku_hilang']) ?></span></div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-people me-2 text-success"></i>Keanggotaan Santri</h6>
                <a href="<?= url('santri') ?>" class="text-success small fw-semibold text-decoration-none">Kelola &rarr;</a>
            </div>
            <div class="d-flex justify-content-around text-center py-2">
                <div><span class="fs-4 fw-bold text-dark d-block"><?= number_format($statSantri['total_santri']) ?></span><small class="text-muted">Total</small></div>
                <div class="border-start"></div>
                <div><span class="fs-4 fw-bold text-success d-block"><?= number_format($statSantri['santri_aktif']) ?></span><small class="text-muted">Aktif</small></div>
                <div class="border-start"></div>
                <div><span class="fs-4 fw-bold text-warning d-block"><?= number_format($statSantri['santri_meminjam']) ?></span><small class="text-muted">Pinjam</small></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-modern">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Tren Peminjaman Buku Bulanan</h6>
            <div style="height: 260px;"><canvas id="chartBulanan"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Kategori Terpopuler</h6>
            <div style="height: 260px;"><canvas id="chartKategori"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-modern p-0 overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-clock-history me-2 text-success"></i>Transaksi Sirkulasi Terbaru</h6>
                <a href="<?= url('transaksi') ?>" class="text-success small fw-semibold text-decoration-none">Lihat Semua &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>Kode TRX</th><th>Peminjam</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Buku</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (empty($recentTransactions)): ?><tr><td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr><?php else: ?>
                        <?php foreach ($recentTransactions as $rt): ?>
                        <tr>
                            <td><a href="<?= url('transaksi/detail/' . $rt['id']) ?>" class="fw-bold text-success text-decoration-none"><?= sanitize($rt['kode_transaksi']) ?></a></td>
                            <td><span class="fw-semibold text-dark d-block"><?= sanitize($rt['nama_santri']) ?></span><small class="text-muted font-monospace"><?= sanitize($rt['nis']) ?> | <?= sanitize($rt['kelas']) ?></small></td>
                            <td><?= format_tanggal($rt['tanggal_pinjam']) ?></td>
                            <td><?= format_tanggal($rt['tanggal_jatuh_tempo']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $rt['total_buku'] ?> Buku</span></td>
                            <td><span class="badge-status <?= $rt['status'] ?>"><?= strtoupper(str_replace('_', ' ', $rt['status'])) ?></span></td>
                        </tr>
                        <?php endforeach; ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-danger m-0"><i class="bi bi-bell-fill me-2"></i>Peringatan Terlambat</h6>
                <span class="badge bg-danger"><?= count($overdueLoans) ?> Santri</span>
            </div>
            <?php if (empty($overdueLoans)): ?>
            <div class="text-center py-4 text-muted"><i class="bi bi-shield-check text-success fs-1 d-block mb-1"></i><small>Tidak ada pinjaman lewat jatuh tempo.</small></div>
            <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($overdueLoans as $ol): ?>
                <div class="list-group-item px-0 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark small"><?= sanitize($ol['nama_santri']) ?></div>
                        <small class="text-muted"><?= sanitize($ol['kelas']) ?> &bull; <?= sanitize($ol['kode_transaksi']) ?></small>
                    </div>
                    <span class="badge bg-danger text-white">+<?= $ol['hari_terlambat'] ?> Hari</span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctxB = document.getElementById('chartBulanan');
    if (ctxB) {
        new Chart(ctxB, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($chartBulanan, 'bulan')) ?>,
                datasets: [{ label: 'Peminjaman', data: <?= json_encode(array_column($chartBulanan, 'total')) ?>, backgroundColor: '#166534', borderRadius: 6 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    const ctxK = document.getElementById('chartKategori');
    if (ctxK) {
        new Chart(ctxK, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($kategoriChart, 'kategori')) ?>,
                datasets: [{ data: <?= json_encode(array_column($kategoriChart, 'total')) ?>, backgroundColor: ['#166534', '#047857', '#22c55e', '#86efac', '#d97706', '#0284c7', '#6366f1'] }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
});
</script>
