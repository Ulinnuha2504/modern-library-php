<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-dark m-0">Log Aktivitas & Audit Trail</h4><p class="text-muted small mb-0">Catatan riwayat transaksi dan operasional data sistem.</p></div>
    <span class="badge bg-secondary">Immutable Log</span>
</div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table-modern">
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Modul</th><th>Aksi</th><th>Deskripsi</th><th>IP</th></tr></thead>
        <tbody>
            <?php foreach ($pagination['data'] as $l): ?>
            <tr>
                <td class="font-monospace small"><?= format_tanggal($l['created_at'], true) ?></td>
                <td><strong><?= sanitize($l['nama_user'] ?? 'Sistem') ?></strong></td>
                <td><span class="badge bg-success-subtle text-success"><?= sanitize($l['module']) ?></span></td>
                <td class="font-monospace fw-bold small"><?= sanitize($l['action']) ?></td>
                <td class="small"><?= sanitize($l['description']) ?></td>
                <td class="font-monospace small text-muted"><?= sanitize($l['ip_address'] ?: '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
