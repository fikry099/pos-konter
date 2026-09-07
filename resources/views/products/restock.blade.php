@extends('layouts.app')

@section('content')
<div class="space-y-3.5 pb-16">

    <!-- 1. HEADER HALAMAN & TOMBOL MODAL RESTOK -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-boxes-packing text-indigo-600 mr-2 text-lg sm:text-xl"></i> Restok Produk & Barang Masuk
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">
                Update kuantitas stok barang masuk cabang <span class="font-bold text-indigo-600">{{ auth()->user()->store?->name ?? 'Toko' }}</span> tanpa mengubah harga katalog
            </p>
        </div>

        <div class="flex items-center shrink-0">
            <button type="button" onclick="openRestockModal()" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs px-4 py-2 rounded-xl font-extrabold transition shadow-md shadow-indigo-200 flex items-center space-x-1.5 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Restok Produk</span>
            </button>
        </div>
    </div>

    <!-- NOTIFIKASI SUKSES / ERROR -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-xl text-xs font-bold flex items-center space-x-2 shadow-xs">
            <i class="fa-solid fa-circle-check text-sm text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2. TAB KATEGORI UTAMA -->
    @include('products.restock.partials.category-tabs')

    <!-- 3. BAR PENCARIAN & SUB-FILTER DINAMIS -->
    @include('products.restock.partials.search-filter')

    <!-- 4. TABEL KATALOG PRODUK -->
    @include('products.restock.partials.product-table')

    <!-- 5. CONTAINER PAGINASI (DINAMIS DARI JAVASCRIPT) -->
    <div id="pagination_container" class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-600">
        <div class="flex items-center space-x-2">
            <span>Tampilkan</span>
            <select id="items_per_page" onchange="changePerPage()" class="bg-slate-50 border border-slate-200 rounded-xl px-2 py-1 font-bold text-slate-800 focus:outline-none cursor-pointer">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>baris per halaman</span>
        </div>

        <div id="pagination_info" class="text-slate-500 font-medium text-[11px] sm:text-xs">
            Menampilkan 0 dari 0 item
        </div>

        <div id="pagination_buttons" class="flex items-center space-x-1">
            <!-- Tombol halaman akan di-generate otomatis via JS -->
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- MODAL POPUP RESTOK -->
@include('products.restock.partials.restock-modal')

<script>
    let activeCategory = 'all';
    let activeSubFilter = '';

    // --- VARIABEL UTAMA PAGINASI ---
    let currentPage = 1;
    let itemsPerPage = 10;
    let visibleRows = [];

    const categoryBrandMap = {
        'all': {
            label: 'Filter Cepat:',
            items: [
                { name: 'Semua', key: '' },
                { name: 'Voucher', key: 'voucher' },
                { name: 'Perdana', key: 'perdana' },
                { name: 'Proteksi', key: 'proteksi' },
                { name: 'Power', key: 'power' },
                { name: 'Audio', key: 'audio' },
                { name: 'Penyimpanan', key: 'penyimpanan' }
            ]
        },
        'voucher': {
            label: 'Operator Voucher:',
            items: [
                { name: 'Semua Operator', key: '' },
                { name: 'Telkomsel', key: 'telkomsel' },
                { name: 'Indosat', key: 'indosat' },
                { name: 'XL Axiata', key: 'xl' },
                { name: 'Tri (3)', key: 'tri' },
                { name: 'Axis', key: 'axis' },
                { name: 'Smartfren', key: 'smartfren' }
            ]
        },
        'perdana': {
            label: 'Operator Perdana:',
            items: [
                { name: 'Semua Operator', key: '' },
                { name: 'Telkomsel', key: 'telkomsel' },
                { name: 'Indosat', key: 'indosat' },
                { name: 'XL Axiata', key: 'xl' },
                { name: 'Tri (3)', key: 'tri' },
                { name: 'Axis', key: 'axis' },
                { name: 'Smartfren', key: 'smartfren' }
            ]
        },
        'aksesoris': {
            label: 'Sub Kategori Aksesoris:',
            items: [
                { name: 'Semua Aksesoris', key: '' },
                { name: 'Proteksi', key: 'proteksi' },
                { name: 'Power', key: 'power' },
                { name: 'Audio', key: 'audio' },
                { name: 'Penyimpanan', key: 'penyimpanan' },
                { name: 'Mount & Stand', key: 'mount' }
            ]
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        renderSubFilterPills('all');
        filterProductsTable();
    });

    function switchCategoryTab(catKey, btnElement) {
        activeCategory = catKey.toLowerCase();
        activeSubFilter = '';

        document.querySelectorAll('.category-tab').forEach(btn => {
            btn.className = 'category-tab bg-slate-50 text-slate-700 hover:bg-slate-100 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shrink-0 border border-slate-200/80 whitespace-nowrap cursor-pointer';
        });
        btnElement.className = 'category-tab active-tab bg-indigo-600 text-white px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 shrink-0 shadow-md shadow-indigo-200 whitespace-nowrap cursor-pointer';

        renderSubFilterPills(activeCategory);
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
            btn.className = `prov-pill ${isDefault ? 'bg-indigo-100 text-indigo-800 border border-indigo-200 font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'} text-[11px] px-3 py-1 rounded-lg shrink-0 transition whitespace-nowrap cursor-pointer`;
            btn.innerText = item.name;
            container.appendChild(btn);
        });
    }

    function filterSubCategory(subKeyword, btnElement) {
        activeSubFilter = subKeyword.toLowerCase();

        document.querySelectorAll('.prov-pill').forEach(btn => {
            btn.className = 'prov-pill bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] px-3 py-1 rounded-lg shrink-0 transition whitespace-nowrap cursor-pointer';
        });
        btnElement.className = 'prov-pill bg-indigo-100 text-indigo-800 border border-indigo-200 text-[11px] font-bold px-3 py-1 rounded-lg shrink-0 transition whitespace-nowrap cursor-pointer';

        filterProductsTable();
    }

    const subCategoryMapping = {
        'proteksi': ['proteksi', 'casing', 'tempered-glass', 'hydrogel'],
        'power': ['power', 'charger', 'kabel-data', 'power-bank'],
        'audio': ['audio', 'tws', 'headset', 'bluetooth-speaker'],
        'penyimpanan': ['penyimpanan', 'flashdisk', 'memory-card'],
        'mount': ['mount-stand', 'holder', 'ring-light-tripod']
    };

    function filterProductsTable() {
        let searchKeyword = document.getElementById('search_product_input').value.toLowerCase().trim();
        let rows = document.querySelectorAll('.product-row');
        
        visibleRows = [];

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
            } else if (activeCategory === 'aksesoris') {
                matchCat = catData.includes('aksesoris') || parentSlug.includes('aksesoris') || grandParentSlug.includes('aksesoris') || parentName.includes('aksesoris');
            } else {
                matchCat = catData.includes(activeCategory);
            }

            let matchSub = true;
            if (activeSubFilter !== '') {
                let allowedKeys = subCategoryMapping[activeSubFilter] || [activeSubFilter];
                let isMatched = allowedKeys.some(key => 
                    catSlug.includes(key) || parentSlug.includes(key) || catName.includes(key) || parentName.includes(key) || name.includes(key)
                );
                matchSub = isMatched;
            }

            let matchSearch = true;
            if (searchKeyword !== '') {
                matchSearch = name.includes(searchKeyword) || code.includes(searchKeyword) || catData.includes(searchKeyword);
            }

            if (matchCat && matchSub && matchSearch) {
                visibleRows.push(row);
            } else {
                row.classList.add('hidden');
            }
        });

        currentPage = 1;
        renderPagination();
    }

    // --- SISTEM PAGINASI CLIENT-SIDE ---

    function renderPagination() {
        let totalItems = visibleRows.length;
        let totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;

        let start = (currentPage - 1) * itemsPerPage;
        let end = start + itemsPerPage;

        // Sembunyikan semua baris terlebih dahulu
        document.querySelectorAll('.product-row').forEach(row => row.classList.add('hidden'));

        // Tampilkan hanya baris yang masuk ke halaman aktif
        visibleRows.slice(start, end).forEach(row => row.classList.remove('hidden'));

        // Update teks informasi item
        let infoEl = document.getElementById('pagination_info');
        if (totalItems === 0) {
            infoEl.innerText = 'Menampilkan 0 dari 0 item';
        } else {
            let showingStart = start + 1;
            let showingEnd = Math.min(end, totalItems);
            infoEl.innerText = `Menampilkan ${showingStart} - ${showingEnd} dari ${totalItems} item`;
        }

        // Render tombol angka paginasi
        let btnContainer = document.getElementById('pagination_buttons');
        btnContainer.innerHTML = '';

        // Tombol Prev
        let prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => goToPage(currentPage - 1);
        prevBtn.className = `px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold transition cursor-pointer ${currentPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-50 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
        btnContainer.appendChild(prevBtn);

        // Angka Halaman
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 5 && (i < currentPage - 1 || i > currentPage + 1) && i !== 1 && i !== totalPages) {
                if (i === currentPage - 2 || i === currentPage + 2) {
                    let dots = document.createElement('span');
                    dots.className = 'px-1.5 text-xs text-slate-400';
                    dots.innerText = '...';
                    btnContainer.appendChild(dots);
                }
                continue;
            }

            let pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.onclick = () => goToPage(i);
            pageBtn.className = `px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer ${i === currentPage ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-50 text-slate-700'}`;
            pageBtn.innerText = i;
            btnContainer.appendChild(pageBtn);
        }

        // Tombol Next
        let nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentPage === totalPages || totalItems === 0;
        nextBtn.onclick = () => goToPage(currentPage + 1);
        nextBtn.className = `px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold transition cursor-pointer ${(currentPage === totalPages || totalItems === 0) ? 'opacity-40 cursor-not-allowed bg-slate-50 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
        btnContainer.appendChild(nextBtn);
    }

    function goToPage(page) {
        currentPage = page;
        renderPagination();
    }

    function changePerPage() {
        itemsPerPage = parseInt(document.getElementById('items_per_page').value) || 10;
        currentPage = 1;
        renderPagination();
    }

    // --- LOGIKA CASCADE HIRARKI KATEGORI DI MODAL RESTOK ---

    function openRestockModal(productId = null, catId = null, parentId = null, grandParentId = null) {
        const modal = document.getElementById('restock_modal');
        const mainSelect = document.getElementById('modal_main_category');
        const subL1Select = document.getElementById('modal_sub_l1_select');
        const subL2Select = document.getElementById('modal_sub_l2_select');
        const productSelect = document.getElementById('modal_product_select');

        // Reset seluruh dropdown modal
        mainSelect.value = '';
        subL1Select.value = '';
        subL2Select.value = '';
        productSelect.value = '';
        
        document.getElementById('modal_sub_l1_container').classList.add('hidden');
        document.getElementById('modal_sub_l2_container').classList.add('hidden');
        document.getElementById('modal_product_container').classList.add('hidden');
        document.getElementById('current_stock_badge_container').classList.add('hidden');
        productSelect.removeAttribute('required');

        // Jika diklik langsung dari tombol baris tabel
        if (productId) {
            let mainCatId = grandParentId ? grandParentId : (parentId ? parentId : catId);
            
            if (mainCatId) {
                mainSelect.value = mainCatId;
                handleModalMainCategoryChange();
                
                if (parentId && parentId !== mainCatId) {
                    subL1Select.value = parentId;
                    handleModalSubL1Change();
                } else if (catId !== mainCatId) {
                    subL1Select.value = catId;
                    handleModalSubL1Change();
                }

                if (grandParentId && catId) {
                    subL2Select.value = catId;
                    handleModalSubL2Change();
                }
            } else {
                document.getElementById('modal_product_container').classList.remove('hidden');
                document.querySelectorAll('.modal-product-option').forEach(opt => opt.classList.remove('hidden'));
            }

            productSelect.value = productId;
            productSelect.setAttribute('required', 'required');
            updateProductStockBadge();
        }
        
        modal.classList.remove('hidden');
    }

    function closeRestockModal() {
        document.getElementById('restock_modal').classList.add('hidden');
    }

    // 1. HANDLER KATEGORI UTAMA (LEVEL 1)
    function handleModalMainCategoryChange() {
        let mainSelect = document.getElementById('modal_main_category');
        let parentId = String(mainSelect.value);
        let selectedOption = mainSelect.options[mainSelect.selectedIndex];
        let catName = selectedOption ? (selectedOption.getAttribute('data-name') || '') : '';

        let subL1Container = document.getElementById('modal_sub_l1_container');
        let subL1Select = document.getElementById('modal_sub_l1_select');
        let subL1Label = document.getElementById('modal_sub_l1_label');
        
        let subL2Container = document.getElementById('modal_sub_l2_container');
        let subL2Select = document.getElementById('modal_sub_l2_select');
        let productContainer = document.getElementById('modal_product_container');

        subL1Select.value = '';
        subL2Select.value = '';
        subL2Container.classList.add('hidden');
        productContainer.classList.add('hidden');
        document.getElementById('current_stock_badge_container').classList.add('hidden');

        if (!parentId) {
            subL1Container.classList.add('hidden');
            return;
        }

        if (catName.includes('pulsa') || catName.includes('paket') || catName.includes('voucher') || catName.includes('perdana')) {
            subL1Label.innerHTML = '2. Pilih Provider <span class="text-rose-500">*</span>';
        } else {
            subL1Label.innerHTML = '2. Sub-Kategori / Jenis <span class="text-rose-500">*</span>';
        }

        let l1Options = subL1Select.querySelectorAll('.modal-sub-l1-option');
        let hasChild = false;

        l1Options.forEach(opt => {
            if (String(opt.getAttribute('data-parent')) === parentId) {
                opt.classList.remove('hidden');
                hasChild = true;
            } else {
                opt.classList.add('hidden');
            }
        });

        if (hasChild) {
            subL1Container.classList.remove('hidden');
        } else {
            subL1Container.classList.add('hidden');
            showModalProducts(parentId, '', '');
        }
    }

    // 2. HANDLER SUB-KATEGORI (LEVEL 2)
    function handleModalSubL1Change() {
        let mainId = String(document.getElementById('modal_main_category').value);
        let subL1Select = document.getElementById('modal_sub_l1_select');
        let subL1Id = String(subL1Select.value);

        let subL2Container = document.getElementById('modal_sub_l2_container');
        let subL2Select = document.getElementById('modal_sub_l2_select');
        let productContainer = document.getElementById('modal_product_container');

        subL2Select.value = '';
        productContainer.classList.add('hidden');
        document.getElementById('current_stock_badge_container').classList.add('hidden');

        if (subL1Id) {
            let l2Options = subL2Select.querySelectorAll('.modal-sub-l2-option');
            let hasChild = false;

            l2Options.forEach(opt => {
                if (String(opt.getAttribute('data-parent')) === subL1Id) {
                    opt.classList.remove('hidden');
                    hasChild = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            if (hasChild) {
                subL2Container.classList.remove('hidden');
            } else {
                subL2Container.classList.add('hidden');
                showModalProducts(mainId, subL1Id, '');
            }
        } else {
            subL2Container.classList.add('hidden');
        }
    }

    // 3. HANDLER SUB-LEVEL 2 (LEVEL 3)
    function handleModalSubL2Change() {
        let mainId = String(document.getElementById('modal_main_category').value);
        let subL1Id = String(document.getElementById('modal_sub_l1_select').value);
        let subL2Id = String(document.getElementById('modal_sub_l2_select').value);

        if (subL2Id) {
            showModalProducts(mainId, subL1Id, subL2Id);
        } else {
            document.getElementById('modal_product_container').classList.add('hidden');
            document.getElementById('current_stock_badge_container').classList.add('hidden');
        }
    }

    // 4. MENGAPLIKASIKAN FILTER DAN MENAMPILKAN DROPDOWN PRODUK
    function showModalProducts(mainId, subL1Id, subL2Id) {
        let productContainer = document.getElementById('modal_product_container');
        let productSelect = document.getElementById('modal_product_select');
        let productOptions = document.querySelectorAll('.modal-product-option');

        productSelect.value = '';
        let hasMatchingProduct = false;

        productOptions.forEach(opt => {
            let catId = String(opt.getAttribute('data-category-id'));
            let parentId = String(opt.getAttribute('data-parent-id'));
            let grandParentId = String(opt.getAttribute('data-grandparent-id'));

            let isMatch = false;

            if (subL2Id) {
                isMatch = (catId === subL2Id);
            } else if (subL1Id) {
                isMatch = (catId === subL1Id || parentId === subL1Id);
            } else if (mainId) {
                isMatch = (catId === mainId || parentId === mainId || grandParentId === mainId);
            }

            if (isMatch) {
                opt.classList.remove('hidden');
                hasMatchingProduct = true;
            } else {
                opt.classList.add('hidden');
            }
        });

        if (hasMatchingProduct) {
            productContainer.classList.remove('hidden');
            productSelect.setAttribute('required', 'required');
        } else {
            productContainer.classList.add('hidden');
            productSelect.removeAttribute('required');
        }
    }

    // FUNCTION UNTUK MENG-UPDATE INFO STOK DI ATAS FORM INPUT
    function updateProductStockBadge() {
        let productSelect = document.getElementById('modal_product_select');
        let selectedOption = productSelect.options[productSelect.selectedIndex];
        let badgeContainer = document.getElementById('current_stock_badge_container');
        let stockCountEl = document.getElementById('current_stock_count');

        if (selectedOption && selectedOption.value) {
            let stock = selectedOption.getAttribute('data-stock') || 0;
            stockCountEl.innerText = stock;
            badgeContainer.classList.remove('hidden');
        } else {
            badgeContainer.classList.add('hidden');
        }
    }
</script>
@endpush