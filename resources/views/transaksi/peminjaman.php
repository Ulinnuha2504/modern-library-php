<div class="mb-4"><h4 class="fw-bold text-dark m-0">Layanan Peminjaman Buku</h4><p class="text-muted small">Cari Santri &rarr; Pilih Buku &rarr; Konfirmasi Transaksi</p></div>
<form action="<?= url('transaksi/peminjaman') ?>" method="POST" id="formPeminjaman">
    <?= csrf_field() ?>
    <input type="hidden" name="santri_id" id="selectedSantriId" value="">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <h6 class="fw-bold text-dark mb-3">1. Pilih Santri Peminjam</h6>
                <div class="mb-3 position-relative">
                    <input type="text" id="inputCariSantri" class="form-control" placeholder="Ketik NIS atau nama santri..." autocomplete="off">
                    <div id="hasilSantriDropdown" class="list-group position-absolute w-100 shadow mt-1 d-none" style="z-index:1050;max-height:220px;overflow-y:auto;"></div>
                </div>
                <div id="santriSelectedCard" class="p-3 border rounded bg-light d-none">
                    <div class="d-flex justify-content-between">
                        <div><h6 class="fw-bold text-dark mb-0" id="santriNama">-</h6><small class="text-muted" id="santriDetail">-</small></div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnResetSantri">&times;</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <h6 class="fw-bold text-dark mb-3">2. Pilih Buku</h6>
                <div class="mb-3 position-relative">
                    <input type="text" id="inputCariBuku" class="form-control" placeholder="Scan barcode atau cari judul buku..." autocomplete="off">
                    <div id="hasilBukuDropdown" class="list-group position-absolute w-100 shadow mt-1 d-none" style="z-index:1050;max-height:220px;overflow-y:auto;"></div>
                </div>
                <div id="cartBooksContainer" class="d-flex flex-column gap-2"><small class="text-muted text-center py-3 border rounded bg-light" id="emptyCartHint">Belum ada buku dipilih.</small></div>
            </div>
        </div>
        <div class="col-12">
            <div class="card-modern d-flex justify-content-between align-items-center">
                <div><small class="text-muted">Durasi Pinjam: <?= $lamaPeminjaman ?> Hari &bull; Batas: <?= $maksimalPinjaman ?> Buku</small></div>
                <button type="submit" class="btn btn-primary-green" id="btnSubmitPinjam" disabled>Konfirmasi Peminjaman</button>
            </div>
        </div>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const inS = document.getElementById('inputCariSantri'), dropS = document.getElementById('hasilSantriDropdown'), cardS = document.getElementById('santriSelectedCard'), hidS = document.getElementById('selectedSantriId'), btnResS = document.getElementById('btnResetSantri');
    const inB = document.getElementById('inputCariBuku'), dropB = document.getElementById('hasilBukuDropdown'), cart = document.getElementById('cartBooksContainer'), hint = document.getElementById('emptyCartHint'), btnSub = document.getElementById('btnSubmitPinjam');
    let books = [];
    inS.addEventListener('input', debounce(async () => {
        const q = inS.value.trim();
        if (q.length < 2) { dropS.classList.add('d-none'); return; }
        const res = await fetchApi('<?= url('santri/search-ajax') ?>?q=' + encodeURIComponent(q));
        dropS.innerHTML = '';
        if (!res.ok || !res.data || res.data.length === 0) { dropS.innerHTML = '<div class="list-group-item text-muted small">Tidak ditemukan.</div>'; dropS.classList.remove('d-none'); return; }
        res.data.forEach(s => {
            const a = document.createElement('a'); a.href='#'; a.className='list-group-item list-group-item-action small';
            a.innerHTML = `<strong>${s.nama}</strong> (${s.nis} - ${s.kelas})`;
            a.onclick = (e) => { e.preventDefault(); hidS.value = s.id; document.getElementById('santriNama').textContent = s.nama; document.getElementById('santriDetail').textContent = s.nis + ' | ' + s.kelas; cardS.classList.remove('d-none'); inS.classList.add('d-none'); dropS.classList.add('d-none'); check(); };
            dropS.appendChild(a);
        });
        dropS.classList.remove('d-none');
    }, 200));
    btnResS.onclick = () => { hidS.value = ''; cardS.classList.add('d-none'); inS.classList.remove('d-none'); inS.value = ''; check(); };
    inB.addEventListener('input', debounce(async () => {
        const q = inB.value.trim();
        if (q.length < 1) { dropB.classList.add('d-none'); return; }
        const res = await fetchApi('<?= url('buku/search-ajax') ?>?q=' + encodeURIComponent(q));
        dropB.innerHTML = '';
        if (!res.ok || !res.data || res.data.length === 0) { dropB.innerHTML = '<div class="list-group-item text-muted small">Buku tidak tersedia.</div>'; dropB.classList.remove('d-none'); return; }
        res.data.forEach(b => {
            const a = document.createElement('a'); a.href='#'; a.className='list-group-item list-group-item-action small d-flex justify-content-between';
            a.innerHTML = `<span><strong>${b.judul}</strong> (${b.kode_buku})</span><span class="badge bg-success-subtle text-success">${b.jumlah_tersedia} ada</span>`;
            a.onclick = (e) => { e.preventDefault(); dropB.classList.add('d-none'); inB.value = ''; if (!books.some(x => x.id == b.id)) { books.push(b); renderCart(); } };
            dropB.appendChild(a);
        });
        dropB.classList.remove('d-none');
    }, 200));
    function renderCart() {
        document.querySelectorAll('.cart-row').forEach(e => e.remove());
        if (books.length > 0) hint.classList.add('d-none'); else hint.classList.remove('d-none');
        books.forEach((b, idx) => {
            const d = document.createElement('div'); d.className = 'cart-row p-2 border rounded bg-white d-flex justify-content-between align-items-center small';
            d.innerHTML = `<div><input type="hidden" name="buku_ids[]" value="${b.id}"><strong>#${idx+1} ${b.judul}</strong> (${b.kode_buku})</div><button type="button" class="btn btn-sm btn-outline-danger py-0">&times;</button>`;
            d.querySelector('button').onclick = () => { books = books.filter(x => x.id != b.id); renderCart(); };
            cart.appendChild(d);
        });
        check();
    }
    function check() { btnSub.disabled = !(hidS.value !== '' && books.length > 0); }
});
</script>
