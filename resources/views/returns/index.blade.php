@extends('layouts.app')

@section('content')
<div class="space-y-3.5">
    
    <!-- HEADER HALAMAN -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-rotate-left text-indigo-600 mr-2 text-lg sm:text-xl"></i> Retur & Penukaran Barang
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Proses penukaran barang customer dan lihat riwayat transaksi retur.</p>
        </div>
    </div>

    <!-- GRID KONTEN UTAMA (2 KOLOM: FORM KIRI, TABEL KANAN) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 items-start">

        <!-- SISI KIRI: FORM INPUT RETUR BARU (COL-SPAN 5) -->
        <div class="lg:col-span-5 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-2.5 flex items-center justify-between">
                <h2 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-square-plus text-indigo-600 mr-1.5 text-sm"></i> Input Retur Baru
                </h2>
            </div>

            <form action="{{ route('returns.store') }}" method="POST" id="returnForm" class="space-y-3.5">
                @csrf
                <input type="hidden" name="transaction_id" id="transaction_id">

                <!-- SEARCH INVOICE NOTA ASLI -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
                        Kode Nota / Invoice <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center space-x-1.5">
                        <input type="text" id="invoice_code_input" placeholder="Misal: TRX-20260930-001" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase">
                        <button type="button" onclick="searchInvoice()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer shrink-0">
                            Cari
                        </button>
                    </div>
                </div>

                <!-- CONTAINER INFORMASI NOTA TRANSAKSI -->
                <div id="invoiceResultContainer" class="hidden space-y-3 bg-slate-50/80 p-3 rounded-xl border border-slate-200/80">
                    <div class="text-[11px] font-bold text-slate-700 flex justify-between border-b border-slate-200 pb-1.5">
                        <span>Detail Transaksi:</span>
                        <span id="trxDate" class="text-slate-400 font-normal"></span>
                    </div>

                    <!-- TABEL ITEM BARANG YANG DIKETAHUI DARI NOTA ASLI -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold text-rose-600 uppercase">Pilih Barang Dikembalikan:</span>
                        <div id="returnedItemsList" class="space-y-1.5 max-h-40 overflow-y-auto pr-1 text-xs">
                            <!-- Injected via Javascript -->
                        </div>
                    </div>
                </div>

                <!-- PILIH BARANG PENGGANTI (BERDASARKAN HIRARKI KATALOG POS) -->
                <div id="replacementContainer" class="hidden space-y-2 border-t border-slate-100 pt-3">
                    <div class="flex justify-between items-center">
                        <label class="block text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider">
                            Pilih Barang Pengganti
                        </label>
                        <button type="button" onclick="addReplacementItemFromHierarchy()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0">
                            + Tambah Item
                        </button>
                    </div>

                    <!-- DROPDOWN BERTINGKAT KATALOG POS -->
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[9px] font-bold text-slate-500 uppercase">1. Kategori Utama</label>
                                <select id="main_category_select" onchange="onMainCategoryChange()" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-medium focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <option value="">-- Pilih Kategori --</option>
                                    @if(isset($categories))
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-[9px] font-bold text-slate-500 uppercase">2. Sub-Kategori POS</label>
                                <select id="sub_category_select" onchange="onSubCategoryChange()" disabled class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-medium focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100 disabled:text-slate-400 cursor-pointer">
                                    <option value="">-- Pilih Sub --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[9px] font-bold text-slate-500 uppercase">3. Produk Fisik</label>
                                <select id="product_select_hierarchy" disabled class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100 disabled:text-slate-400 cursor-pointer">
                                    <option value="">-- Pilih Produk --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- LIST ITEM BARANG PENGGANTI YANG SUDAH DITAMBAHKAN -->
                    <div id="replacementItemsList" class="space-y-1.5 max-h-36 overflow-y-auto pr-1 text-xs pt-1">
                        <!-- Injected via Javascript -->
                    </div>
                </div>

                <!-- ALASAN RETUR -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
                        Alasan Retur / Catatan
                    </label>
                    <input type="text" name="reason" placeholder="Misal: Barang salah ukuran / cacat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- RINGKASAN NOMINAL RETUR -->
                <div id="summaryContainer" class="hidden bg-slate-100 p-3 rounded-xl space-y-1 font-mono text-xs font-bold border border-slate-200">
                    <div class="flex justify-between text-rose-600">
                        <span>Total Retur:</span>
                        <span id="txtReturnedTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-emerald-600">
                        <span>Total Pengganti:</span>
                        <span id="txtReplacementTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-800 border-t border-slate-200 pt-1 text-sm font-black">
                        <span>Selisih:</span>
                        <span id="txtDifference">Rp 0</span>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" id="btnSubmitReturn" disabled class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-extrabold py-2.5 rounded-xl text-xs transition shadow-md shadow-indigo-200 flex items-center justify-center space-x-1.5 cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Proses & Simpan Retur</span>
                </button>
            </form>
        </div>

        <!-- SISI KANAN: TABEL RIWAYAT RETUR (COL-SPAN 7) -->
        <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-3.5 border-b border-slate-100">
                <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider">
                    Riwayat Retur Terbaru
                </h3>
            </div>
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse min-w-[550px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-400 text-[10px] uppercase font-extrabold">
                            <th class="py-2.5 px-3">Kode / Waktu</th>
                            <th class="py-2.5 px-3">Nota Asli</th>
                            <th class="py-2.5 px-3">Kasir</th>
                            <th class="py-2.5 px-3 text-right">Nilai Tukar</th>
                            <th class="py-2.5 px-3 text-center">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse($returns as $ret)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="py-2.5 px-3">
                                    <div class="font-mono font-bold text-slate-800 text-[11px]">{{ $ret->return_code }}</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5 whitespace-nowrap">
                                        {{ $ret->created_at->format('d/m/Y H:i') }} WIB
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <span class="font-mono font-bold text-indigo-600 text-[10px] bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100">
                                        {{ $ret->transaction->invoice_code ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <div class="text-[11px] font-bold text-slate-700">{{ $ret->user->name ?? 'Kasir' }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-[11px] whitespace-nowrap">
                                    <div class="text-rose-600 font-bold">Retur: Rp {{ number_format($ret->returned_total, 0, ',', '.') }}</div>
                                    <div class="text-emerald-600 font-bold">Baru: Rp {{ number_format($ret->replacement_total, 0, ',', '.') }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono whitespace-nowrap">
                                    @if($ret->price_difference > 0)
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[9px] px-1.5 py-0.5 rounded-full">
                                            +Rp {{ number_format($ret->price_difference, 0, ',', '.') }}
                                        </span>
                                    @elseif($ret->price_difference < 0)
                                        <span class="bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[9px] px-1.5 py-0.5 rounded-full">
                                            -Rp {{ number_format(abs($ret->price_difference), 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[9px] px-1.5 py-0.5 rounded-full">
                                            Rp 0
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-medium">
                                    <i class="fa-solid fa-rotate-left text-3xl mb-2 text-slate-300 block"></i>
                                    <p class="text-xs font-bold text-slate-600">Belum ada riwayat retur barang.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100 bg-slate-50">
                {{ $returns->links() }}
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
let activeTransaction = null;
let replacementItems = [];

// DATA KATEGORI REKURSIF & PRODUK DARI CONTROLLER
const categoriesData = @json($categories ?? []);
const productsData   = @json($products ?? []);

// FILTER KHUSUS PRODUK FISIK (Non-Digital / Non-Pulsa)
const physicalProducts = productsData.filter(p => p.type === 'physical');

function searchInvoice() {
    let code = document.getElementById('invoice_code_input').value.trim();
    if (!code) return alert('Masukkan kode nota terlebih dahulu!');

    fetch(`{{ route('returns.search') }}?invoice_code=${code}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                activeTransaction = res.data;
                document.getElementById('transaction_id').value = activeTransaction.id;
                document.getElementById('trxDate').innerText = new Date(activeTransaction.created_at).toLocaleString('id-ID');
                
                renderReturnedItemsList(activeTransaction.details);
                
                document.getElementById('invoiceResultContainer').classList.remove('hidden');
                document.getElementById('replacementContainer').classList.remove('hidden');
                document.getElementById('summaryContainer').classList.remove('hidden');
            } else {
                alert(res.message || 'Nota tidak ditemukan!');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mencari nota transaksi.');
        });
}

function renderReturnedItemsList(details) {
    let container = document.getElementById('returnedItemsList');
    container.innerHTML = '';

    details.forEach(function(detail) {
        let prodName = detail.product ? detail.product.name : 'Produk';
        let priceFormatted = parseInt(detail.selling_price).toLocaleString('id-ID');

        let defaultQty = detail.qty > 0 ? 1 : 0;

        let html = '<div class="flex items-center justify-between bg-white p-2 rounded-lg border border-slate-200 text-xs">' +
            '<div>' +
                '<div class="font-bold text-slate-800">' + prodName + '</div>' +
                '<div class="text-[10px] text-slate-400">Rp ' + priceFormatted + ' / Pcs</div>' +
            '</div>' +
            '<div class="flex items-center space-x-1">' +
                '<input type="number" name="returned_items[' + detail.product_id + '][qty]" value="' + defaultQty + '" min="0" max="' + detail.qty + '" onchange="calculateSummary()" class="w-14 bg-slate-50 border border-slate-200 rounded px-1.5 py-1 text-center font-bold text-xs">' +
                '<input type="hidden" name="returned_items[' + detail.product_id + '][id]" value="' + detail.product_id + '">' +
                '<span class="text-[10px] text-slate-400">/ ' + detail.qty + ' Pcs</span>' +
            '</div>' +
        '</div>';

        container.innerHTML += html;
    });

    calculateSummary();
}

function onMainCategoryChange() {
    let mainCatId = document.getElementById('main_category_select').value;
    let subSelect = document.getElementById('sub_category_select');
    let prodSelect = document.getElementById('product_select_hierarchy');

    subSelect.innerHTML = '<option value="">-- Pilih Sub --</option>';
    subSelect.disabled = true;
    prodSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
    prodSelect.disabled = true;

    if (!mainCatId) return;

    let selectedParent = categoriesData.find(c => c.id == mainCatId);
    if (!selectedParent) return;

    let children = selectedParent.children || [];

    if (children.length > 0) {
        let subOptions = '<option value="">-- Pilih Sub-Kategori POS --</option>';
        children.forEach(sub => {
            subOptions += `<option value="${sub.id}">${sub.name}</option>`;
        });
        subSelect.innerHTML = subOptions;
        subSelect.disabled = false;
    } else {
        loadProductsForCategory(mainCatId);
    }
}

function onSubCategoryChange() {
    let subCatId = document.getElementById('sub_category_select').value;
    if (!subCatId) return;
    loadProductsForCategory(subCatId);
}

function loadProductsForCategory(categoryId) {
    let prodSelect = document.getElementById('product_select_hierarchy');
    prodSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';

    let targetCatId = parseInt(categoryId);

    // Filter produk yang terikat ke category_id target
    let filtered = physicalProducts.filter(p => parseInt(p.category_id) === targetCatId);

    if (filtered.length > 0) {
        let options = '<option value="">-- Pilih Produk --</option>';
        filtered.forEach(p => {
            options += `<option value="${p.id}" data-name="${p.name}" data-price="${p.selling_price}">${p.name} (Rp ${Number(p.selling_price).toLocaleString('id-ID')})</option>`;
        });
        prodSelect.innerHTML = options;
        prodSelect.disabled = false;
    } else {
        prodSelect.innerHTML = '<option value="">-- Tidak ada produk fisik di sub-kategori ini --</option>';
        prodSelect.disabled = true;
    }
}

function addReplacementItemFromHierarchy() {
    let select = document.getElementById('product_select_hierarchy');
    let option = select.options[select.selectedIndex];
    if (!select.value) return alert('Pilih produk fisik pengganti terlebih dahulu!');

    let id = select.value;
    let name = option.getAttribute('data-name');
    let price = parseFloat(option.getAttribute('data-price'));

    let existing = replacementItems.find(function(item) { return item.id == id; });
    if (existing) {
        existing.qty += 1;
    } else {
        replacementItems.push({ id: id, name: name, price: price, qty: 1 });
    }

    renderReplacementItems();
    calculateSummary();
}

function renderReplacementItems() {
    let container = document.getElementById('replacementItemsList');
    container.innerHTML = '';

    replacementItems.forEach(function(item, index) {
        let html = '<div class="flex items-center justify-between bg-white p-2 rounded-lg border border-slate-200 text-xs">' +
            '<div>' +
                '<div class="font-bold text-slate-800">' + item.name + '</div>' +
                '<div class="text-[10px] text-emerald-600 font-bold">Rp ' + item.price.toLocaleString('id-ID') + '</div>' +
            '</div>' +
            '<div class="flex items-center space-x-1.5">' +
                '<input type="hidden" name="replacement_items[' + index + '][id]" value="' + item.id + '">' +
                '<input type="number" name="replacement_items[' + index + '][qty]" value="' + item.qty + '" min="1" onchange="updateReplacementQty(' + index + ', this.value)" class="w-12 bg-slate-50 border border-slate-200 rounded px-1 py-0.5 text-center font-bold text-xs">' +
                '<button type="button" onclick="removeReplacementItem(' + index + ')" class="text-rose-500 hover:text-rose-700 text-xs">' +
                    '<i class="fa-solid fa-trash"></i>' +
                '</button>' +
            '</div>' +
        '</div>';

        container.innerHTML += html;
    });
}

function updateReplacementQty(index, val) {
    replacementItems[index].qty = parseInt(val) || 1;
    calculateSummary();
}

function removeReplacementItem(index) {
    replacementItems.splice(index, 1);
    renderReplacementItems();
    calculateSummary();
}

function calculateSummary() {
    let returnedTotal = 0;
    if (activeTransaction) {
        activeTransaction.details.forEach(function(detail) {
            let input = document.querySelector('input[name="returned_items[' + detail.product_id + '][qty]"]');
            if (input) {
                let qty = parseInt(input.value) || 0;
                returnedTotal += qty * parseFloat(detail.selling_price);
            }
        });
    }

    let replacementTotal = 0;
    replacementItems.forEach(function(item) {
        replacementTotal += item.qty * item.price;
    });

    let diff = replacementTotal - returnedTotal;

    document.getElementById('txtReturnedTotal').innerText = 'Rp ' + returnedTotal.toLocaleString('id-ID');
    document.getElementById('txtReplacementTotal').innerText = 'Rp ' + replacementTotal.toLocaleString('id-ID');
    document.getElementById('txtDifference').innerText = 'Rp ' + diff.toLocaleString('id-ID');

    let btnSubmit = document.getElementById('btnSubmitReturn');
    btnSubmit.disabled = (returnedTotal <= 0 || replacementTotal <= 0 || diff < 0);
}
</script>
@endpush
@endsection