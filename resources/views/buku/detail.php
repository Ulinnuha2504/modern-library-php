<div class="d-flex justify-content-between align-items-center mb-4">
    <div><a href="<?= url('buku') ?>" class="text-success text-decoration-none small">&larr; Kembali ke Katalog</a><h4 class="fw-bold text-dark mt-1 mb-0"><?= sanitize($buku['judul']) ?></h4></div>
    <?php if (is_role(['admin', 'petugas'])): ?><a href="<?= url('buku/edit/' . $buku['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Ubah Data</a><?php endif; ?>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-modern text-center">
            <?php if ($buku['cover']): ?><img src="<?= url('uploads/covers/' . $buku['cover']) ?>" class="img-fluid rounded mb-3" style="max-height:260px;object-fit:cover;"><?php else: ?><div class="rounded bg-light border p-5 mb-3"><i class="bi bi-book fs-1 text-muted"></i></div><?php endif; ?>
            <h5 class="fw-bold"><?= sanitize($buku['judul']) ?></h5>
            <p class="text-muted small"><?= sanitize($buku['penulis']) ?></p>
            <ul class="list-group list-group-flush text-start small">
                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Kode SKU</span><strong class="font-monospace"><?= sanitize($buku['kode_buku']) ?></strong></li>
                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">ISBN</span><span class="font-monospace"><?= sanitize($buku['isbn'] ?: '-') ?></span></li>
                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Kategori</span><span class="text-success"><?= sanitize($buku['nama_kategori'] ?? '-') ?></span></li>
                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Lokasi Rak</span><span><?= sanitize($buku['kode_rak'] ?? '-') ?></span></li>
            </ul>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-modern mb-4">
            <h6 class="fw-bold text-dark mb-3">Kondisi Stok Eksemplar</h6>
            <div class="row g-2 text-center mb-3">
                <div class="col-3"><div class="p-2 border rounded bg-light"><small class="text-muted d-block" style="font-size:0.7rem;">TOTAL</small><span class="fs-4 fw-bold"><?= $buku['jumlah_total'] ?></span></div></div>
                <div class="col-3"><div class="p-2 border rounded bg-light"><small class="text-success d-block" style="font-size:0.7rem;">TERSEDIA</small><span class="fs-4 fw-bold text-success"><?= $buku['jumlah_tersedia'] ?></span></div></div>
                <div class="col-3"><div class="p-2 border rounded bg-light"><small class="text-warning d-block" style="font-size:0.7rem;">DIPINJAM</small><span class="fs-4 fw-bold text-warning"><?= $buku['jumlah_dipinjam'] ?></span></div></div>
                <div class="col-3"><div class="p-2 border rounded bg-light"><small class="text-danger d-block" style="font-size:0.7rem;">RUSAK/HILANG</small><span class="fs-4 fw-bold text-danger"><?= $buku['jumlah_rusak'] + $buku['jumlah_hilang'] ?></span></div></div>
            </div>
            <h6 class="fw-bold text-dark mb-1">Sinopsis</h6>
            <p class="small text-muted"><?= nl2br(sanitize($buku['deskripsi'] ?: 'Tidak ada sinopsis.')) ?></p>
        </div>
        <div class="card-modern p-0 overflow-hidden">
            <div class="p-3 border-bottom bg-light"><h6 class="fw-bold text-dark m-0">Riwayat Sirkulasi Peminjaman</h6></div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead><tr><th>No. TRX</th><th>Peminjam</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Denda</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (empty($borrowHistory)): ?><tr><td colspan="6" class="text-center py-3 text-muted small">Belum ada riwayat.</td></tr><?php else: ?>
                        <?php foreach ($borrowHistory as $h): ?>
                        <tr><td><a href="<?= url('transaksi/detail/' . $h['peminjaman_id']) ?>" class="fw-bold text-success text-decoration-none"><?= sanitize($h['kode_transaksi']) ?></a></td><td><?= sanitize($h['nama_santri']) ?></td><td><?= format_tanggal($h['tanggal_pinjam']) ?></td><td><?= format_tanggal($h['tanggal_jatuh_tempo']) ?></td><td><?= $h['denda'] > 0 ? format_rupiah($h['denda']) : '-' ?></td><td><span class="badge-status <?= $h['status'] ?>"><?= strtoupper($h['status']) ?></span></td></tr>
                        <?php endforeach; ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
