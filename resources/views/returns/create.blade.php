@extends('layouts.app')

@push('styles')
<!-- SWEETALERT2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="w-full">
    
    <!-- HEADER HALAMAN DENGAN TOMBOL KEMBALI IKON < DI KIRI -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('returns.index') }}" class="bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center shrink-0 border border-slate-200 cursor-pointer" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                    <i class="fa-solid fa-right-left text-indigo-600 mr-2 text-lg sm:text-xl"></i> Form Penukaran & Retur Barang
                </h1>
                <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Cari nota lama customer untuk memproses retur dan barang pengganti.</p>
            </div>
        </div>
    </div>

    <!-- PADA ONSUBMIT, KITA PANGGIL FUNGSI REINDEX AGAR AMAN DIKIRIM KE CONTROLLER -->
    <form action="{{ route('returns.store') }}" method="POST" id="returnForm" autocomplete="off" onsubmit="return prepareAndValidateForm()" class="space-y-3.5">
        @csrf
        <input type="hidden" name="transaction_id" id="input_transaction_id">

        <!-- LANGKAH 1: CARI NOTA / INVOICE -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 space-y-3">
            <h3 class="text-[10px] sm:text-xs font-extrabold text-slate-700 uppercase tracking-wider">1. Cari Nota / Invoice Asli Pembelian</h3>
            <div class="flex gap-2">
                <input type="text" id="search_invoice_input" autocomplete="off" placeholder="Contoh: TRX-20260902-1234" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="button" onclick="searchInvoice()" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2 rounded-xl text-xs font-extrabold transition shadow-md shadow-indigo-200 cursor-pointer whitespace-nowrap">
                    Cari Nota
                </button>
            </div>
            <div id="invoice_alert" class="hidden text-xs font-semibold p-3 rounded-xl"></div>
        </div>

        <!-- LANGKAH 2: DETAIL ITEM DARI NOTA (YANG DIKEMBALIKAN) -->
        <div id="returned_section" class="hidden bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="text-[10px] sm:text-xs font-extrabold text-rose-600 uppercase tracking-wider">2. Pilih Item yang Dikembalikan oleh Customer</h3>
                <span class="text-xs font-black text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200" id="returned_total_display">Total Retur: Rp 0</span>
            </div>
            <div id="returned_items_container" class="space-y-2">
                <!-- Diisi via JavaScript -->
            </div>
        </div>

        <!-- LANGKAH 3: PILIH ITEM PENGGANTI DENGAN HIERARKI KATEGORI FISIK -->
        <div id="replacement_section" class="hidden bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2.5">
                <div>
                    <h3 class="text-[10px] sm:text-xs font-extrabold text-emerald-600 uppercase tracking-wider">3. Pilih Barang Pengganti (Khusus Produk Fisik)</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium">Syarat: Total harga barang pengganti harus di atas atau sama dengan nominal barang retur.</p>
                </div>
                <button type="button" onclick="addReplacementRow()" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-extrabold px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0">
                    + Tambah Item Pengganti
                </button>
            </div>
            
            <div id="replacement_items_container" class="space-y-3">
                <!-- Baris Produk Pengganti Berhierarki akan ditambahkan di sini -->
            </div>

            <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                <span class="text-xs font-bold text-slate-700">Total Nilai Barang Pengganti:</span>
                <span class="text-xs font-black text-emerald-600" id="replacement_total_display">Rp 0</span>
            </div>
        </div>

        <!-- LANGKAH 4: ALASAN & SUBMIT -->
        <div id="checkout_section" class="hidden bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 space-y-3">
            <!-- RINGKASAN SELISIH -->
            <div id="price_diff_alert" class="p-3 rounded-xl text-xs font-bold flex justify-between items-center bg-indigo-50 text-indigo-700 border border-indigo-200">
                <span>Selisih Tambah Bayar (Customer Nombok):</span>
                <span class="text-xs sm:text-sm font-black" id="diff_amount_display">Rp 0</span>
            </div>

            <div>
                <label class="block text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan Penukaran / Retur</label>
                <input type="text" name="reason" required autocomplete="off" placeholder="Contoh: Salah tipe casing, tidak muat di HP orang tua" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" id="submit_btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 rounded-xl text-xs sm:text-sm transition shadow-md shadow-indigo-200 cursor-pointer">
                Proses & Simpan Retur Barang
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<!-- SWEETALERT2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SCRIPTS & DATA HIERARKI -->
<script>
    const categoriesData = @json($categories);
    const productsData = @json($products);

    let currentTransactionDetails = [];

    function isDigitalCategory(catName, catSlug) {
        let name = (catName || '').toLowerCase();
        let slug = (catSlug || '').toLowerCase();
        return name.includes('pulsa') || slug.includes('pulsa') ||
               name.includes('ewallet') || name.includes('e-wallet') || name.includes('wallet') || slug.includes('e-wallet') ||
               name.includes('bank') || name.includes('transfer') || slug.includes('bank') ||
               name.includes('pln') || name.includes('token') || slug.includes('pln') ||
               (name.includes('paket') && !name.includes('perdana')) || slug.includes('paket-data');
    }

    const physicalCategories = categoriesData.filter(cat => !isDigitalCategory(cat.name, cat.slug));
    const physicalProducts = productsData.filter(p => p.type === 'physical');

    function searchInvoice() {
        let code = document.getElementById('search_invoice_input').value.trim();
        let alertEl = document.getElementById('invoice_alert');
        let retSec = document.getElementById('returned_section');
        let repSec = document.getElementById('replacement_section');
        let chkSec = document.getElementById('checkout_section');

        if (!code) {
            Swal.fire({
                icon: 'warning',
                title: 'Kode Nota Kosong',
                text: 'Silakan masukkan kode invoice/nota terlebih dahulu!',
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });

            alertEl.className = "text-xs font-semibold p-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-200";
            alertEl.innerText = "Masukkan kode invoice terlebih dahulu!";
            alertEl.classList.remove('hidden');
            return;
        }

        fetch(`{{ route('returns.search') }}?invoice_code=${code}`)
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(res => {
                if(res.status === 'success') {
                    let trx = res.data;
                    currentTransactionDetails = trx.details;
                    document.getElementById('input_transaction_id').value = trx.id;

                    alertEl.className = "text-xs font-semibold p-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200";
                    alertEl.innerText = `Nota Ditemukan! Kasir: ${trx.user ? trx.user.name : '-'} (${trx.created_at})`;
                    alertEl.classList.remove('hidden');

                    let container = document.getElementById('returned_items_container');
                    container.innerHTML = '';
                    
                    // Kita buat elemen baris retur dengan atribut data agar mudah dibaca saat re-index submit
                    trx.details.forEach((det, idx) => {
                        let priceFormatted = Number(det.selling_price).toLocaleString();
                        container.innerHTML += `
                            <div class="returned-item-row flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200" data-product-id="${det.product_id}">
                                <div class="flex items-center space-x-2.5">
                                    <input type="checkbox" id="ret_check_${idx}" value="${det.product_id}" checked onchange="calculateTotals()" class="ret-checkbox w-4 h-4 text-indigo-600 rounded cursor-pointer">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">${det.product ? det.product.name : 'Produk'}</span>
                                        <span class="text-[10px] sm:text-[11px] font-semibold text-indigo-600">Harga: Rp ${priceFormatted} / pcs</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-mono text-slate-500">Qty:</span>
                                    <input type="number" id="ret_qty_${idx}" value="${det.qty}" min="1" max="${det.qty}" autocomplete="off" onchange="calculateTotals()" onkeyup="calculateTotals()" class="ret-qty-input w-14 bg-white border border-slate-300 rounded-lg px-2 py-1 text-xs font-bold text-center">
                                </div>
                            </div>
                        `;
                    });

                    retSec.classList.remove('hidden');
                    repSec.classList.remove('hidden');
                    chkSec.classList.remove('hidden');
                    
                    document.getElementById('replacement_items_container').innerHTML = '';
                    replacementIndex = 0;
                    addReplacementRow();
                    calculateTotals();
                }
            })
            .catch(err => {
                let errorMsg = err.message || "Nota transaksi tidak ditemukan atau salah kode!";

                Swal.fire({
                    icon: 'error',
                    title: 'Nota Tidak Ditemukan!',
                    text: errorMsg,
                    confirmButtonColor: '#e11d48',
                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
                });

                alertEl.className = "text-xs font-semibold p-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-200";
                alertEl.innerText = errorMsg;
                alertEl.classList.remove('hidden');
                retSec.classList.add('hidden');
                repSec.classList.add('hidden');
                chkSec.classList.add('hidden');
            });
    }

    let replacementIndex = 0;

    function addReplacementRow() {
        let container = document.getElementById('replacement_items_container');
        let rowId = 'rep_row_' + replacementIndex;

        let mainCatOptions = '<option value="">-- Pilih Kategori Utama --</option>';
        physicalCategories.forEach(cat => {
            mainCatOptions += `<option value="${cat.id}">${cat.name}</option>`;
        });

        let rowHtml = `
            <div class="replacement-row bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2.5 relative" id="${rowId}">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-extrabold text-slate-700">Item Pengganti</span>
                    <button type="button" onclick="document.getElementById('${rowId}').remove(); calculateTotals();" class="text-rose-500 hover:text-rose-700 text-xs font-bold cursor-pointer">
                        <i class="fa-solid fa-trash mr-1"></i> Hapus Baris
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1">1. Kategori Utama</label>
                        <select onchange="onMainCatChange(${replacementIndex})" id="main_cat_${replacementIndex}" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                            ${mainCatOptions}
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-indigo-900 mb-1">2. Sub-Kategori</label>
                        <select onchange="onSubCatChange(${replacementIndex})" id="sub_cat_${replacementIndex}" disabled class="w-full bg-indigo-50/50 border border-indigo-200 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-800 focus:outline-none disabled:bg-slate-100 disabled:text-slate-400 cursor-pointer">
                            <option value="">-- Pilih Sub-Kategori --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-emerald-900 mb-1">3. Pilih Produk Fisik</label>
                        <select id="product_${replacementIndex}" onchange="calculateTotals()" disabled class="replacement-product-select w-full bg-emerald-50/50 border border-emerald-200 rounded-xl px-2.5 py-1.5 text-xs font-black text-slate-800 focus:outline-none disabled:bg-slate-100 disabled:text-slate-400 cursor-pointer">
                            <option value="">-- Pilih Produk --</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-0.5">
                    <span class="text-xs font-bold text-indigo-600" id="rep_price_info_${replacementIndex}">Harga: Rp 0</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono text-slate-500 font-bold">Qty:</span>
                        <input type="number" id="rep_qty_${replacementIndex}" value="1" min="1" autocomplete="off" onchange="calculateTotals()" onkeyup="calculateTotals()" class="replacement-qty-input w-16 bg-white border border-slate-300 rounded-lg px-2 py-1 text-xs font-black text-center">
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', rowHtml);
        replacementIndex++;
        calculateTotals();
    }

    function onMainCatChange(index) {
        let mainCatId = document.getElementById(`main_cat_${index}`).value;
        let subSelect = document.getElementById(`sub_cat_${index}`);
        let prodSelect = document.getElementById(`product_${index}`);

        subSelect.innerHTML = '<option value="">-- Pilih Sub-Kategori --</option>';
        subSelect.disabled = true;
        prodSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
        prodSelect.disabled = true;

        if (!mainCatId) return;

        let selectedParent = physicalCategories.find(c => c.id == mainCatId);
        if (selectedParent && selectedParent.children && selectedParent.children.length > 0) {
            let subOptions = '<option value="">-- Pilih Sub-Kategori --</option>';
            selectedParent.children.forEach(sub => {
                subOptions += `<option value="${sub.id}">${sub.name}</option>`;
            });
            subSelect.innerHTML = subOptions;
            subSelect.disabled = false;
        } else {
            loadProductsRecursive(index, mainCatId);
        }
    }

    function onSubCatChange(index) {
        let subCatId = document.getElementById(`sub_cat_${index}`).value;
        let prodSelect = document.getElementById(`product_${index}`);
        
        prodSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
        prodSelect.disabled = true;

        if (!subCatId) return;

        loadProductsRecursive(index, subCatId);
    }

    function loadProductsRecursive(index, categoryId) {
        let prodSelect = document.getElementById(`product_${index}`);

        let validCategoryIds = [parseInt(categoryId)];
        
        function fetchSubIds(catId) {
            physicalCategories.forEach(pCat => {
                if (pCat.id == catId && pCat.children) {
                    pCat.children.forEach(child => {
                        validCategoryIds.push(child.id);
                        fetchSubIds(child.id);
                    });
                }
                if (pCat.children) {
                    pCat.children.forEach(sub => {
                        if (sub.id == catId && sub.children) {
                            sub.children.forEach(grandChild => {
                                validCategoryIds.push(grandChild.id);
                            });
                        }
                    });
                }
            });
        }
        fetchSubIds(categoryId);

        let filteredProducts = physicalProducts.filter(p => validCategoryIds.includes(parseInt(p.category_id)));

        let prodOptions = '<option value="">-- Pilih Produk --</option>';
        if (filteredProducts.length > 0) {
            filteredProducts.forEach(p => {
                let stockVal = p.stock !== undefined ? p.stock : 0;
                let priceVal = p.selling_price !== undefined ? Number(p.selling_price).toLocaleString() : 0;
                prodOptions += `<option value="${p.id}" data-price="${p.selling_price}">${p.name} (Stok: ${stockVal} | Rp ${priceVal})</option>`;
            });
            prodSelect.innerHTML = prodOptions;
            prodSelect.disabled = false;
        } else {
            prodSelect.innerHTML = '<option value="">-- Tidak ada produk di kategori ini --</option>';
            prodSelect.disabled = true;
        }
    }

    function calculateTotals() {
        let totalReturned = 0;
        let totalReplacement = 0;

        currentTransactionDetails.forEach((det, idx) => {
            let checkEl = document.getElementById(`ret_check_${idx}`);
            let qtyEl = document.getElementById(`ret_qty_${idx}`);

            if (checkEl && checkEl.checked && qtyEl) {
                let qty = parseInt(qtyEl.value) || 0;
                totalReturned += (det.selling_price * qty);
            }
        });

        // Hitung ulang berdasarkan baris replacement yang aktif saat ini
        let repRows = document.querySelectorAll('.replacement-row');
        repRows.forEach((row, i) => {
            let prodSelect = row.querySelector('.replacement-product-select');
            let qtyEl = row.querySelector('.replacement-qty-input');
            let priceInfo = row.querySelector('[id^="rep_price_info_"]');

            if (prodSelect && prodSelect.value && qtyEl) {
                let selectedOption = prodSelect.options[prodSelect.selectedIndex];
                let price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                let qty = parseInt(qtyEl.value) || 0;

                if (priceInfo) {
                    priceInfo.innerText = `Harga: Rp ${Number(price).toLocaleString()} / pcs`;
                }

                totalReplacement += (price * qty);
            }
        });

        document.getElementById('returned_total_display').innerText = `Total Retur: Rp ${Number(totalReturned).toLocaleString()}`;
        document.getElementById('replacement_total_display').innerText = `Rp ${Number(totalReplacement).toLocaleString()}`;

        let diff = totalReplacement - totalReturned;
        let diffAlert = document.getElementById('price_diff_alert');
        let submitBtn = document.getElementById('submit_btn');

        if (diff < 0) {
            diffAlert.className = "p-3 rounded-xl text-xs font-bold flex justify-between items-center bg-rose-50 text-rose-700 border border-rose-200";
            diffAlert.innerHTML = `
                <span>Gagal! Nominal barang pengganti harus di atas/sama dengan barang retur (Kurang Rp ${Number(Math.abs(diff)).toLocaleString()}):</span>
                <span class="text-xs sm:text-sm font-black text-rose-700">Rp ${Number(diff).toLocaleString()}</span>
            `;
            submitBtn.disabled = true;
            submitBtn.className = "w-full bg-slate-300 text-slate-500 font-extrabold py-3 rounded-xl text-xs sm:text-sm transition cursor-not-allowed";
        } else {
            diffAlert.className = "p-3 rounded-xl text-xs font-bold flex justify-between items-center bg-emerald-50 text-emerald-700 border border-emerald-200";
            diffAlert.innerHTML = `
                <span>Selisih Tambah Bayar (Customer Nombok):</span>
                <span class="text-xs sm:text-sm font-black text-emerald-700">+Rp ${Number(diff).toLocaleString()}</span>
            `;
            submitBtn.disabled = false;
            submitBtn.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 rounded-xl text-xs sm:text-sm transition shadow-md shadow-indigo-200 cursor-pointer";
        }
    }

    // FUNGSI UTAMA UNTUK MEMBERSIHKAN DAN MERANGKAI ULANG NAMA INPUT ARRAY (RE-INDEXING) SEBELUM DIKIRIM KE CONTROLLER
    function prepareAndValidateForm() {
        let retTotalText = document.getElementById('returned_total_display').innerText.replace(/[^0-9]/g, '');
        let repTotalText = document.getElementById('replacement_total_display').innerText.replace(/[^0-9]/g, '');

        let retTotal = parseInt(retTotalText) || 0;
        let repTotal = parseInt(repTotalText) || 0;

        if (repTotal < retTotal) {
            Swal.fire({
                icon: 'warning',
                title: 'Nominal Tidak Mencukupi',
                text: 'Penukaran barang tidak boleh lebih murah dari barang yang dikembalikan!',
                confirmButtonColor: '#e11d48',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });
            return false;
        }

        // Hapus elemen input hidden sisa re-index sebelumnya jika ada
        document.querySelectorAll('.dynamic-clean-input').forEach(el => el.remove());

        let form = document.getElementById('returnForm');

        // 1. Re-index returned_items yang dicentang saja
        let returnedRows = document.querySelectorAll('.returned-item-row');
        let retIndex = 0;
        returnedRows.forEach((row, oldIdx) => {
            let checkbox = row.querySelector('.ret-checkbox');
            let qtyInput = row.querySelector('.ret-qty-input');

            if (checkbox && checkbox.checked) {
                let inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = `returned_items[${retIndex}][id]`;
                inputId.value = checkbox.value;
                inputId.className = 'dynamic-clean-input';
                form.appendChild(inputId);

                let inputQty = document.createElement('input');
                inputQty.type = 'hidden';
                inputQty.name = `returned_items[${retIndex}][qty]`;
                inputQty.value = qtyInput.value;
                inputQty.className = 'dynamic-clean-input';
                form.appendChild(inputQty);

                retIndex++;
            }
        });

        if (retIndex === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Item Dipilih',
                text: 'Pilih minimal 1 item yang dikembalikan oleh customer!',
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });
            return false;
        }

        // 2. Re-index replacement_items yang valid
        let repRows = document.querySelectorAll('.replacement-row');
        let repIndex = 0;
        let validRepCount = 0;

        repRows.forEach(row => {
            let prodSelect = row.querySelector('.replacement-product-select');
            let qtyInput = row.querySelector('.replacement-qty-input');

            if (prodSelect && prodSelect.value) {
                let inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = `replacement_items[${repIndex}][id]`;
                inputId.value = prodSelect.value;
                inputId.className = 'dynamic-clean-input';
                form.appendChild(inputId);

                let inputQty = document.createElement('input');
                inputQty.type = 'hidden';
                inputQty.name = `replacement_items[${repIndex}][qty]`;
                inputQty.value = qtyInput.value;
                inputQty.className = 'dynamic-clean-input';
                form.appendChild(inputQty);

                repIndex++;
                validRepCount++;
            }
        });

        if (validRepCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Barang Pengganti Kosong',
                text: 'Pilih minimal 1 barang pengganti untuk customer!',
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });
            return false;
        }

        return true;
    }
</script>
@endpush