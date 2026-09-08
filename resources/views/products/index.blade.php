@extends('layouts.app')

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="space-y-3 -mt-6 sm:-mt-8 pb-36 sm:pb-16">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-boxes-stacked text-indigo-600 mr-2 text-lg sm:text-xl"></i> Kelola Katalog & Stok
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Total {{ count($products) }} produk terdaftar dalam sistem konter</p>
        </div>

        <a href="{{ route('products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-extrabold px-3.5 py-2.5 rounded-xl shadow-md shadow-indigo-200 transition flex items-center justify-center space-x-1.5 shrink-0">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Produk Baru</span>
        </a>
    </div>

    <div class="bg-white p-1.5 sm:p-2 rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center space-x-1.5 overflow-x-auto no-scrollbar py-0.5 px-0.5">
            <button type="button" onclick="switchCategoryTab('all', this)" class="category-tab active-tab bg-indigo-600 text-white px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 shrink-0 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-border-all text-xs"></i>
                <span>Semua ({{ count($products) }})</span>
            </button>

            <button type="button" onclick="switchCategoryTab('pulsa', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-mobile-screen-button text-indigo-500"></i>
                <span>Pulsa</span>
            </button>

            <button type="button" onclick="switchCategoryTab('voucher', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-ticket text-blue-500"></i>
                <span>Voucher</span>
            </button>

            <button type="button" onclick="switchCategoryTab('perdana', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-sim-card text-rose-500"></i>
                <span>Perdana</span>
            </button>

            <button type="button" onclick="switchCategoryTab('ewallet', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-wallet text-emerald-500"></i>
                <span>E-Wallet</span>
            </button>

            <button type="button" onclick="switchCategoryTab('bank', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-building-columns text-teal-500"></i>
                <span>Transfer</span>
            </button>

            <button type="button" onclick="switchCategoryTab('pln', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-bolt text-amber-500"></i>
                <span>PLN</span>
            </button>

            <button type="button" onclick="switchCategoryTab('aksesoris', this)" class="category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plug text-purple-500"></i>
                <span>Aksesoris</span>
            </button>
        </div>
    </div>

    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm space-y-2.5">
        <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar py-0.5">
            <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider shrink-0 mr-1" id="filter_label">FILTER:</span>
            <div id="provider_pills_container" class="flex items-center space-x-1.5 shrink-0"></div>
        </div>

        <div class="relative w-full">
            <input type="text" id="search_product_input" onkeyup="filterProductsTable()" placeholder="Cari nama produk / SKU..." class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white font-bold text-slate-800 transition">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-extrabold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-3">Produk & SKU</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3 text-right">Modal / Jual</th>
                        <th class="py-3 px-3 text-right">Margin</th>
                        <th class="py-3 px-3 text-center">Stok</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($products as $product)
                        @php
                            $currentCat = $product->category;
                            $parentCat = $currentCat ? $currentCat->parent : null;
                            $grandParentCat = $parentCat ? $parentCat->parent : null;

                            $catSlug = strtolower($currentCat->slug ?? '');
                            $catNameLower = strtolower($currentCat->name ?? '');
                            $parentSlug = strtolower($parentCat->slug ?? '');
                            $parentNameLower = strtolower($parentCat->name ?? '');
                            $grandParentSlug = strtolower($grandParentCat->slug ?? '');

                            $catHierarchyText = '';
                            if ($grandParentCat) { $catHierarchyText .= $grandParentCat->name . ' > '; }
                            if ($parentCat) { $catHierarchyText .= $parentCat->name . ' > '; }
                            $catHierarchyText .= $currentCat->name ?? 'Tanpa Kategori';

                            $catSearchData = strtolower($catHierarchyText);
                            $prodName = strtolower($product->name);
                            $prodCode = strtolower($product->code ?? '');
                            $margin = $product->selling_price - $product->cost_price;
                        @endphp
                        <tr class="product-row hover:bg-indigo-50/40 transition"
                            data-category="{{ $catSearchData }}"
                            data-cat-slug="{{ $catSlug }}"
                            data-cat-name="{{ $catNameLower }}"
                            data-parent-slug="{{ $parentSlug }}"
                            data-parent-name="{{ $parentNameLower }}"
                            data-grand-parent-slug="{{ $grandParentSlug }}"
                            data-name="{{ $prodName }}"
                            data-code="{{ $prodCode }}">
                            
                            <td class="py-3 px-3 min-w-[140px]">
                                <div class="font-extrabold text-slate-800 text-xs leading-snug">{{ $product->name }}</div>
                                <div class="flex items-center space-x-1 mt-1">
                                    <span class="text-[10px] font-mono text-slate-400">{{ $product->code ?? '-' }}</span>
                                    <span class="px-1.5 py-0.5 rounded font-black uppercase text-[9px] {{ $product->type === 'digital' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $product->type === 'digital' ? 'Digital' : 'Fisik' }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-3 px-3">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200 inline-block whitespace-nowrap">
                                    @if($parentCat)
                                        <span class="text-slate-400 font-normal">{{ $parentCat->name }} &rsaquo;</span>
                                    @endif
                                    <span class="text-indigo-700 font-extrabold">{{ $currentCat->name ?? 'Tanpa Kategori' }}</span>
                                </span>
                            </td>

                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                <div class="text-[10px] text-slate-400 font-mono">M: Rp {{ number_format($product->cost_price, 0, ',', '.') }}</div>
                                <div class="font-black text-indigo-700 font-mono text-xs mt-0.5">J: Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                            </td>

                            <td class="py-3 px-3 text-right font-bold font-mono text-xs whitespace-nowrap {{ $margin > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                +Rp {{ number_format($margin, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-3 text-center font-bold text-xs whitespace-nowrap">
                                @if($product->type === 'physical')
                                    <span class="{{ $product->stock <= $product->min_stock ? 'text-rose-600 font-black animate-pulse' : 'text-slate-800' }}">
                                        {{ $product->stock }} Pcs
                                    </span>
                                @else
                                    <span class="text-slate-400 font-mono text-base">∞</span>
                                @endif
                            </td>

                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('products.edit', $product->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-100 transition">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-100 transition">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty_row_db">
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-4xl mb-2 text-slate-300 block"></i>
                                <p class="text-xs font-bold text-slate-500">Belum ada data produk terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse

                    <tr id="no_matching_products_row" class="hidden">
                        <td colspan="7" class="py-10 text-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-3xl mb-2 text-slate-300 block"></i>
                            <p class="text-xs font-bold text-slate-500">Tidak ada produk yang cocok dengan pencarian / filter ini.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="table_pagination_container" class="p-3 border-t border-slate-100 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center justify-between w-full sm:w-auto space-x-2 text-slate-500 font-bold">
                <span id="pagination_info_text">Menampilkan 0 dari 0 produk</span>
                <div class="flex items-center space-x-1 shrink-0">
                    <span class="text-[11px] font-normal">Tampil:</span>
                    <select id="items_per_page_select" onchange="changePerPage()" class="bg-white border border-slate-200 rounded-lg text-xs font-bold px-1.5 py-1 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div id="pagination_buttons_wrapper" class="flex items-center space-x-1 shrink-0 justify-center w-full sm:w-auto">
                </div>
        </div>
    </div>

</div>

<script>
    let activeCategory = 'all';
    let activeSubFilter = '';

    // VARIABEL PAGINASI CLIENT-SIDE
    let currentPage = 1;
    let itemsPerPage = 10;
    let matchedRowsList = [];

    const categoryBrandMap = {
        'all': {
            label: 'FILTER:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'Proteksi', key: 'proteksi' },
                { name: 'Power', key: 'power' },
                { name: 'Audio', key: 'audio' },
                { name: 'Penyimpanan', key: 'penyimpanan' },
                { name: 'Telkomsel', key: 'telkomsel' },
                { name: 'Indosat', key: 'indosat' },
                { name: 'XL Axiata', key: 'xl' }
            ]
        },
        'pulsa': {
            label: 'OPERATOR:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'Telkomsel', key: 'telkomsel' },
                { name: 'Indosat', key: 'indosat' },
                { name: 'XL Axiata', key: 'xl' },
                { name: 'Tri (3)', key: 'three' },
                { name: 'Axis', key: 'axis' },
                { name: 'Smartfren', key: 'smartfren' }
            ]
        },
        'voucher': {
            label: 'OPERATOR:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'Telkomsel', key: 'telkomsel' },
                { name: 'Indosat', key: 'indosat' },
                { name: 'XL Axiata', key: 'xl' },
                { name: 'Tri (3)', key: 'three' },
                { name: 'Axis', key: 'axis' },
                { name: 'Smartfren', key: 'smartfren' }
            ]
        },
        'perdana': {
            label: 'OPERATOR:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'Telkomsel', key: 'telkomsel' },
                { name: 'Indosat', key: 'indosat' },
                { name: 'XL Axiata', key: 'xl' },
                { name: 'Tri (3)', key: 'three' },
                { name: 'Axis', key: 'axis' },
                { name: 'Smartfren', key: 'smartfren' }
            ]
        },
        'ewallet': {
            label: 'PENYEDIA:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'DANA', key: 'dana' },
                { name: 'OVO', key: 'ovo' },
                { name: 'GoPay', key: 'gopay' },
                { name: 'ShopeePay', key: 'shopee' },
                { name: 'LinkAja', key: 'linkaja' }
            ]
        },
        'bank': {
            label: 'BANK:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'BCA', key: 'bca' },
                { name: 'BRI', key: 'bri' },
                { name: 'Mandiri', key: 'mandiri' },
                { name: 'BNI', key: 'bni' },
                { name: 'BSI', key: 'bsi' }
            ]
        },
        'pln': {
            label: 'LAYANAN:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'PLN Token', key: 'token' },
                { name: 'PLN Tagihan', key: 'tagihan' }
            ]
        },
        'aksesoris': {
            label: 'AKSESORIS:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'Proteksi', key: 'proteksi' },
                { name: 'Power', key: 'power' },
                { name: 'Audio', key: 'audio' },
                { name: 'Penyimpanan', key: 'penyimpanan' },
                { name: 'Mount & Stand', key: 'mount' }
            ]
        }
    };

    const subCategoryMapping = {
        'proteksi': ['proteksi', 'casing', 'tempered-glass', 'hydrogel'],
        'power': ['power', 'charger', 'kabel-data', 'power-bank'],
        'audio': ['audio', 'tws', 'headset', 'bluetooth-speaker'],
        'penyimpanan': ['penyimpanan', 'flashdisk', 'memory-card'],
        'mount': ['mount-stand', 'holder', 'ring-light-tripod']
    };

    document.addEventListener('DOMContentLoaded', function() {
        renderSubFilterPills('all');
        filterProductsTable();
    });

    function switchCategoryTab(catKey, btnElement) {
        activeCategory = catKey.toLowerCase();
        activeSubFilter = '';

        document.querySelectorAll('.category-tab').forEach(btn => {
            btn.className = 'category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 active:scale-95 cursor-pointer';
        });
        btnElement.className = 'category-tab active-tab bg-indigo-600 text-white px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 shrink-0 shadow-sm active:scale-95 cursor-pointer';

        renderSubFilterPills(activeCategory);
        currentPage = 1;
        filterProductsTable();
    }

    function renderSubFilterPills(categoryKey) {
        let container = document.getElementById('provider_pills_container');
        let labelEl = document.getElementById('filter_label');
        
        let config = categoryBrandMap[categoryKey] || categoryBrandMap['all'];
        
        labelEl.innerText = config.label;
        container.innerHTML = '';

        config.items.forEach((item, index) => {
            let isDefault = index === 0;
            let btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('onclick', `filterSubCategory('${item.key}', this)`);
            btn.className = `prov-pill ${isDefault ? 'bg-indigo-100 text-indigo-800 border border-indigo-200 font-extrabold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold'} text-[11px] px-3 py-1.5 rounded-xl shrink-0 transition cursor-pointer active:scale-95`;
            btn.innerText = item.name;
            container.appendChild(btn);
        });
    }

    function filterSubCategory(subKeyword, btnElement) {
        activeSubFilter = subKeyword.toLowerCase();

        document.querySelectorAll('.prov-pill').forEach(btn => {
            btn.className = 'prov-pill bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] px-3 py-1.5 rounded-xl shrink-0 transition cursor-pointer active:scale-95';
        });

        btnElement.className = 'prov-pill bg-indigo-100 text-indigo-800 border border-indigo-200 text-[11px] font-extrabold px-3 py-1.5 rounded-xl shrink-0 transition cursor-pointer active:scale-95';

        currentPage = 1;
        filterProductsTable();
    }

    function filterProductsTable() {
        let searchKeyword = document.getElementById('search_product_input').value.toLowerCase().trim();
        let rows = document.querySelectorAll('.product-row');
        matchedRowsList = [];

        rows.forEach(row => {
            let catData = (row.getAttribute('data-category') || '').toLowerCase();
            let catSlug = (row.getAttribute('data-cat-slug') || '').toLowerCase();
            let catName = (row.getAttribute('data-cat-name') || '').toLowerCase();
            let parentSlug = (row.getAttribute('data-parent-slug') || '').toLowerCase();
            let parentName = (row.getAttribute('data-parent-name') || '').toLowerCase();
            let grandParentSlug = (row.getAttribute('data-grand-parent-slug') || '').toLowerCase();
            
            let name = (row.getAttribute('data-name') || '').toLowerCase();
            let code = (row.getAttribute('data-code') || '').toLowerCase();

            let matchCat = false;
            if (activeCategory === 'all') {
                matchCat = true;
            } else if (activeCategory === 'perdana') {
                matchCat = catData.includes('perdana') || catData.includes('kartu') || parentSlug.includes('perdana') || catSlug.includes('perdana');
            } else if (activeCategory === 'bank') {
                matchCat = catData.includes('bank') || catData.includes('transfer') || parentSlug.includes('bank');
            } else if (activeCategory === 'ewallet') {
                matchCat = catData.includes('ewallet') || catData.includes('wallet') || catData.includes('topup') || parentSlug.includes('ewallet');
            } else if (activeCategory === 'pln') {
                matchCat = catData.includes('pln') || catData.includes('token') || parentSlug.includes('pln');
            } else if (activeCategory === 'aksesoris') {
                matchCat = catData.includes('aksesoris') || parentSlug.includes('aksesoris') || grandParentSlug.includes('aksesoris') || parentName.includes('aksesoris');
            } else {
                matchCat = catData.includes(activeCategory);
            }

            let matchSub = true;
            if (activeSubFilter !== '') {
                let allowedKeys = subCategoryMapping[activeSubFilter] || [activeSubFilter];
                
                let isMatched = allowedKeys.some(key => 
                    catSlug.includes(key) || 
                    parentSlug.includes(key) || 
                    catName.includes(key) || 
                    parentName.includes(key) ||
                    name.includes(key)
                );
                
                matchSub = isMatched;
            }

            let matchSearch = true;
            if (searchKeyword !== '') {
                matchSearch = name.includes(searchKeyword) || code.includes(searchKeyword) || catData.includes(searchKeyword);
            }

            if (matchCat && matchSub && matchSearch) {
                matchedRowsList.push(row);
            }
            row.classList.add('hidden'); // Sembunyikan semua terlebih dahulu
        });

        // TAMPILKAN ATAU SEMBUNYIKAN NOTIFIKASI KOSONG
        let noMatchRow = document.getElementById('no_matching_products_row');
        if (rows.length > 0 && matchedRowsList.length === 0) {
            if (noMatchRow) noMatchRow.classList.remove('hidden');
        } else {
            if (noMatchRow) noMatchRow.classList.add('hidden');
        }

        renderClientPagination();
    }

    // FUNGSI UTAMA RENDER PAGINASI CLIENT-SIDE
    function renderClientPagination() {
        let totalItems = matchedRowsList.length;
        let infoText = document.getElementById('pagination_info_text');
        let buttonsWrapper = document.getElementById('pagination_buttons_wrapper');

        if (totalItems === 0) {
            infoText.innerText = "Menampilkan 0 dari 0 produk";
            buttonsWrapper.innerHTML = '';
            return;
        }

        let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        let startIdx = (currentPage - 1) * itemsPerPage;
        let endIdx = Math.min(startIdx + itemsPerPage, totalItems);

        // Tampilkan hanya baris pada halaman aktif
        for (let i = startIdx; i < endIdx; i++) {
            if (matchedRowsList[i]) {
                matchedRowsList[i].classList.remove('hidden');
            }
        }

        infoText.innerText = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} produk`;

        // GENERATE TOMBOL NAVIGASI HALAMAN
        buttonsWrapper.innerHTML = '';

        // Tombol Previous
        let prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => goToPage(currentPage - 1);
        prevBtn.className = `px-2 py-1 rounded-lg text-xs font-bold transition border border-slate-200 cursor-pointer ${currentPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-100 text-slate-700'}`;
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
        buttonsWrapper.appendChild(prevBtn);

        // Nomor Halaman
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 5 && (i < currentPage - 1 || i > currentPage + 1) && i !== 1 && i !== totalPages) {
                if (i === currentPage - 2 || i === currentPage + 2) {
                    let dots = document.createElement('span');
                    dots.className = 'px-1 text-slate-400 text-xs';
                    dots.innerText = '...';
                    buttonsWrapper.appendChild(dots);
                }
                continue;
            }

            let pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.onclick = () => goToPage(i);
            pageBtn.className = `px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer ${i === currentPage ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-100 text-slate-700'}`;
            pageBtn.innerText = i;
            buttonsWrapper.appendChild(pageBtn);
        }

        // Tombol Next
        let nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => goToPage(currentPage + 1);
        nextBtn.className = `px-2 py-1 rounded-lg text-xs font-bold transition border border-slate-200 cursor-pointer ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-100 text-slate-700'}`;
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
        buttonsWrapper.appendChild(nextBtn);
    }

    function goToPage(page) {
        currentPage = page;
        // Sembunyikan dulu baris yang sedang tampil
        matchedRowsList.forEach(row => row.classList.add('hidden'));
        renderClientPagination();
    }

    function changePerPage() {
        let selectEl = document.getElementById('items_per_page_select');
        itemsPerPage = parseInt(selectEl.value) || 10;
        currentPage = 1;
        // Sembunyikan dulu baris yang sedang tampil
        matchedRowsList.forEach(row => row.classList.add('hidden'));
        renderClientPagination();
    }
</script>
@endsection