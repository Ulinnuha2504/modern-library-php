<div class="mb-4"><h4 class="fw-bold text-dark m-0">Layanan Pengembalian Buku & Denda</h4><p class="text-muted small">Cari transaksi berdasarkan Kode Transaksi atau NIS, tentukan kondisi buku, dan simpan pengembalian.</p></div>
<div class="card-modern mb-4">
    <div class="row g-2">
        <div class="col-md-9"><input type="text" id="inQ" class="form-control" placeholder="Scan barcode transaksi (TRX-...) atau NIS..." value="<?= sanitize($kodeSearch) ?>"></div>
        <div class="col-md-3"><button type="button" id="btnC" class="btn btn-primary-green w-100">Cari Transaksi</button></div>
    </div>
</div>
<div id="resArea"><div class="card-modern text-center py-4 text-muted"><small>Masukkan kode transaksi untuk memuat data pengembalian.</small></div></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const inQ = document.getElementById('inQ'), btnC = document.getElementById('btnC'), resArea = document.getElementById('resArea');
    btnC.onclick = search;
    inQ.onkeypress = (e) => { if(e.key === 'Enter'){ e.preventDefault(); search(); } };
    if (inQ.value.trim().length >= 3) search();
    async function search() {
        const q = inQ.value.trim();
        if (q.length < 2) return;
        btnC.disabled = true;
        const res = await fetchApi('<?= url('transaksi/pengembalian/search-ajax') ?>?q=' + encodeURIComponent(q));
        btnC.disabled = false;
        resArea.innerHTML = '';
        if (!res.ok || !res.data || !res.data.data || res.data.data.length === 0) {
            resArea.innerHTML = '<div class="card-modern text-center py-4 text-muted">Tidak ditemukan transaksi aktif.</div>';
            return;
        }
        res.data.data.forEach(trx => {
            const card = document.createElement('div'); card.className = 'card-modern mb-4 border-start border-4 border-success';
            let rowsHtml = '';
            (trx.unreturned_books || []).forEach((b, i) => {
                rowsHtml += `<tr>
                    <td><input type="checkbox" name="items[${i}][detail_id]" value="${b.detail_id}" class="form-check-input check-item" checked></td>
                    <td><strong>${b.judul_buku}</strong><br><small class="text-muted font-monospace">${b.kode_buku}</small></td>
                    <td><select name="items[${i}][kondisi_kembali]" class="form-select form-select-sm sel-k"><option value="baik">Baik (Normal)</option><option value="rusak_ringan">Rusak Ringan (+Rp 5.000)</option><option value="rusak_berat">Rusak Berat (+Rp 20.000)</option><option value="hilang">Buku Hilang (+Rp 40.000)</option></select></td>
                    <td><span class="text-danger fw-bold denda-val">Rp 0</span></td>
                    <td><input type="text" name="items[${i}][catatan]" class="form-control form-control-sm" placeholder="Catatan..."></td>
                </tr>`;
            });
            card.innerHTML = `<form action="<?= url('transaksi/pengembalian/proses') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="peminjaman_id" value="${trx.id}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div><span class="badge bg-success-subtle text-success font-monospace">${trx.kode_transaksi}</span><h5 class="fw-bold text-dark mt-1 mb-0">${trx.nama_santri} (${trx.nis} - ${trx.kelas})</h5></div>
                    <div class="text-end small">Jatuh Tempo: <strong>${trx.tanggal_jatuh_tempo}</strong><br>${trx.hari_terlambat > 0 ? '<span class="badge bg-danger">Terlambat ' + trx.hari_terlambat + ' Hari</span>' : '<span class="badge bg-success">Tepat Waktu</span>'}</div>
                </div>
                <table class="table-modern"><thead><tr><th>Pilih</th><th>Buku</th><th>Kondisi</th><th>Denda</th><th>Catatan</th></tr></thead><tbody>${rowsHtml}</tbody></table>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <div><span class="small text-muted d-block">TOTAL DENDA:</span><span class="fs-5 fw-bold text-danger total-denda">Rp 0</span></div>
                    <button type="submit" class="btn btn-primary-green">Simpan Pengembalian</button>
                </div>
            </form>`;
            function calc() {
                let tot = 0;
                card.querySelectorAll('tbody tr').forEach(r => {
                    const chk = r.querySelector('.check-item').checked;
                    const k = r.querySelector('.sel-k').value;
                    const dDisp = r.querySelector('.denda-val');
                    if (!chk) { dDisp.textContent = 'Rp 0'; return; }
                    let d = trx.hari_terlambat * 1000;
                    if (k === 'rusak_ringan') d += 5000;
                    else if (k === 'rusak_berat') d += 20000;
                    else if (k === 'hilang') d += 40000;
                    tot += d;
                    dDisp.textContent = 'Rp ' + d.toLocaleString('id-ID');
                });
                card.querySelector('.total-denda').textContent = 'Rp ' + tot.toLocaleString('id-ID');
            }
            card.querySelectorAll('.check-item, .sel-k').forEach(e => e.onchange = calc);
            calc();
            resArea.appendChild(card);
        });
    }
});
</script>
