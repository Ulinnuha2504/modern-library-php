<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-dark m-0">Riwayat Transaksi</h4></div>
    <div><a href="<?= url('transaksi/peminjaman') ?>" class="btn btn-primary-green">Pinjam</a> <a href="<?= url('transaksi/pengembalian') ?>" class="btn btn-outline-green">Kembali</a></div>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>Kode TRX</th><th>Santri</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Total Buku</th><th>Denda</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach ($pagination['data'] as $t): ?>
            <tr>
                <td class="font-monospace fw-bold"><a href="<?= url('transaksi/detail/' . $t['id']) ?>" class="text-success text-decoration-none"><?= sanitize($t['kode_transaksi']) ?></a></td>
                <td><strong><?= sanitize($t['nama_santri']) ?></strong><br><small class="text-muted"><?= sanitize($t['nis']) ?> (<?= sanitize($t['kelas']) ?>)</small></td>
                <td><?= format_tanggal($t['tanggal_pinjam']) ?></td><td><?= format_tanggal($t['tanggal_jatuh_tempo']) ?></td>
                <td><?= $t['total_item'] ?> Buku</td><td><?= $t['denda'] > 0 ? '<span class="text-danger fw-bold">' . format_rupiah($t['denda']) . '</span>' : '-' ?></td>
                <td><span class="badge-status <?= $t['status'] ?>"><?= strtoupper(str_replace('_', ' ', $t['status'])) ?></span></td>
                <td><a href="<?= url('transaksi/detail/' . $t['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
