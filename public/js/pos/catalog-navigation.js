// ==========================================
// VARIABLE GLOBAL NAVIGASI KATALOG BERTINGKAT
// ==========================================
let navLevel = 1;
let selectedCategory = '';
let selectedProvider = '';
let currentSubDetailKey = ''; // Menyimpan key sub-detail (proteksi, power, audio, dll)
window.selectedProviderTitle = '';

let posCurrentPage = 1;
let posItemsPerPage = 10;
let posVisibleProducts = [];

function togglePriceFilterVisibility(show) {
    let priceFilter = document.getElementById('price_filter_container');
    let minInput = document.getElementById('filter_min_price');
    let maxInput = document.getElementById('filter_max_price');

    if (priceFilter) {
        if (show) {
            priceFilter.classList.remove('hidden');
            priceFilter.classList.add('flex');
        } else {
            priceFilter.classList.remove('flex');
            priceFilter.classList.add('hidden');
            if (minInput) minInput.value = '';
            if (maxInput) maxInput.value = '';
        }
    }
}

// 1. DARI LEVEL 1 KE LEVEL 2
function navigateToSubCategory(catType, title) {
    navLevel = 2;
    selectedCategory = catType.toLowerCase();
    currentSubDetailKey = '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.remove('hidden');
    document.getElementById('view_products_grid').classList.add('hidden');
    
    togglePriceFilterVisibility(false);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-list mr-2 text-indigo-600"></i> Pilih ' + title;
    }

    // Sembunyikan semua grid sub-kategori
    let grids = [
        'sub_cellular_grid', 'sub_ewallet_grid', 'sub_bank_grid', 
        'sub_handphone_grid', 'sub_aksesoris_grid', 'sub_proteksi_grid', 
        'sub_power_grid', 'sub_audio_grid', 'sub_penyimpanan_grid', 'sub_mount_grid'
    ];
    grids.forEach(g => {
        let el = document.getElementById(g);
        if (el) el.classList.add('hidden');
    });

    let normalizedType = catType.toLowerCase();
    
    if (normalizedType.includes('handphone') || normalizedType === 'hp') {
        let el = document.getElementById('sub_handphone_grid');
        if (el) el.classList.remove('hidden');
    } else if (normalizedType.includes('wallet')) {
        let el = document.getElementById('sub_ewallet_grid');
        if (el) el.classList.remove('hidden');
    } else if (normalizedType.includes('bank') || normalizedType.includes('transfer')) {
        let el = document.getElementById('sub_bank_grid');
        if (el) el.classList.remove('hidden');
    } else if (normalizedType.includes('aksesoris')) {
        let el = document.getElementById('sub_aksesoris_grid');
        if (el) el.classList.remove('hidden');
    } else {
        let el = document.getElementById('sub_cellular_grid');
        if (el) el.classList.remove('hidden');
    }
}

// 2. DARI LEVEL 2 KE LEVEL 3 (SUB-DETAIL AKSESORIS)
function navigateToSubDetail(subKey) {
    navLevel = 3;
    currentSubDetailKey = subKey.toLowerCase();

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.remove('hidden');
    document.getElementById('view_products_grid').classList.add('hidden');

    togglePriceFilterVisibility(false);

    let titleText = document.getElementById('catalog_title_text');
    let titleMap = {
        'proteksi': 'Proteksi (Casing, TG, Hydrogel)',
        'power': 'Power (Charger, Kabel, PB)',
        'audio': 'Audio (TWS, Headset, Speaker)',
        'penyimpanan': 'Penyimpanan (Flashdisk, MicroSD)',
        'mount-stand': 'Mount & Stand (Holder, Tripod)'
    };

    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-layer-group mr-2 text-indigo-600"></i> Pilih ' + (titleMap[currentSubDetailKey] || 'Kategori');
    }

    let grids = [
        'sub_cellular_grid', 'sub_ewallet_grid', 'sub_bank_grid', 
        'sub_handphone_grid', 'sub_aksesoris_grid', 'sub_proteksi_grid', 
        'sub_power_grid', 'sub_audio_grid', 'sub_penyimpanan_grid', 'sub_mount_grid'
    ];
    grids.forEach(g => {
        let el = document.getElementById(g);
        if (el) el.classList.add('hidden');
    });

    let targetGridMap = {
        'proteksi': 'sub_proteksi_grid',
        'power': 'sub_power_grid',
        'audio': 'sub_audio_grid',
        'penyimpanan': 'sub_penyimpanan_grid',
        'mount-stand': 'sub_mount_grid'
    };

    let targetGrid = document.getElementById(targetGridMap[currentSubDetailKey]);
    if (targetGrid) targetGrid.classList.remove('hidden');
}

// 3. SELEKSI SUB-ITEM / PROVIDER UNTUK MENGAMBIL DAFTAR PRODUK
function selectProviderFilter(providerKey, title) {
    navLevel = currentSubDetailKey !== '' ? 4 : 3;
    selectedProvider = providerKey.toLowerCase();
    window.selectedProviderTitle = title || providerKey;

    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    togglePriceFilterVisibility(true);
    
    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Produk ' + title;
    }

    fetchProductsFromServer(selectedCategory, selectedProvider);
}

// 4. MENGARAH LANGSUNG KE PRODUK DARI UTAMA (PLN / LAINNYA)
function navigateToDirectCategory(slugKey, title) {
    navLevel = 3;
    selectedCategory = slugKey.toLowerCase();
    selectedProvider = '';
    currentSubDetailKey = '';
    window.selectedProviderTitle = title || '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    togglePriceFilterVisibility(true);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> ' + title;
    }

    fetchProductsFromServer(selectedCategory, '');
}

// 5. TAMPILKAN SEMUA PRODUK ON-DEMAND
function showAllProducts() {
    navLevel = 3;
    selectedCategory = 'all';
    selectedProvider = '';
    currentSubDetailKey = '';
    window.selectedProviderTitle = '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    togglePriceFilterVisibility(true);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Semua Produk';
    }

    fetchProductsFromServer('all', '');
}

// 6. FETCH AJAX DATA PRODUK
function fetchProductsFromServer(category, provider) {
    let gridView = document.getElementById('view_products_grid');
    if (!gridView) return;

    let container = gridView.querySelector('.grid');
    if (!container) return;

    let pagContainer = document.getElementById('pos_pagination_container');
    if (pagContainer) pagContainer.classList.add('hidden');

    container.innerHTML = `
        <div class="col-span-full bg-white rounded-3xl p-10 text-center text-indigo-600 border border-indigo-100 shadow-sm">
            <i class="fa-solid fa-circle-notch fa-spin text-3xl mb-3"></i>
            <p class="text-sm font-bold text-slate-700">Memuat produk...</p>
        </div>
    `;

    fetch(`/pos/products/by-category?category=${category}&provider=${provider}`)
        .then(response => response.json())
        .then(products => {
            renderProductsHTML(products, container);
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            container.innerHTML = `
                <div class="col-span-full bg-white rounded-3xl p-10 text-center text-rose-500 border border-rose-100">
                    <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
                    <p class="text-sm font-bold">Gagal memuat daftar produk. Silakan coba lagi.</p>
                </div>
            `;
        });
}

// 7. RENDER ITEM PRODUK KETIKA DILOAD
function renderProductsHTML(products, container) {
    container.innerHTML = '';

    let catStr = (selectedCategory || '').toLowerCase();
    let provStr = (selectedProvider || '').toLowerCase();
    
    let isBankOrWallet = catStr.includes('bank') || catStr.includes('ewallet') || catStr.includes('wallet') || catStr.includes('transfer') ||
                         provStr.includes('bank') || provStr.includes('ewallet') || provStr.includes('bca') || provStr.includes('bri') || provStr.includes('bni') || provStr.includes('mandiri') || provStr.includes('dana') || provStr.includes('ovo') || provStr.includes('gopay') || provStr.includes('shopeepay') || provStr.includes('linkaja');

    if (isBankOrWallet) {
        let providerTitleParam = (window.selectedProviderTitle || selectedProvider || '').replace(/'/g, "\\'");

        let customCardHTML = `
            <div id="card_custom_amount" 
                 onclick="openCustomAmountModal('${providerTitleParam}')" 
                 class="product-item bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl shadow-md hover:shadow-indigo-200 p-3.5 flex flex-col justify-between transition cursor-pointer active:scale-98 group"
                 data-name="nominal bebas kustom transfer topup pulsa"
                 data-code="CUSTOM"
                 data-price="0">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full bg-white/20 text-white backdrop-blur-sm">
                            Kustom
                        </span>
                        <i class="fa-solid fa-pen-to-square text-xs text-indigo-200"></i>
                    </div>
                    <h4 class="font-extrabold text-white text-xs md:text-sm line-clamp-2 mb-1 leading-snug">
                        Nominal Bebas / Lainnya
                    </h4>
                    <p class="text-[11px] text-indigo-100 mb-2">Contoh: Rp 7.000, Rp 11.000, Rp 23.000, dll</p>
                </div>

                <div>
                    <div class="text-xs font-semibold text-indigo-200 mb-2">
                        Rp Bebas + Admin
                    </div>
                    <button type="button" class="w-full bg-white text-indigo-700 hover:bg-indigo-50 text-xs font-black py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm active:scale-95">
                        <i class="fa-solid fa-keyboard text-xs"></i>
                        <span>Input Nominal</span>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', customCardHTML);
    }

    if ((!products || products.length === 0) && !isBankOrWallet) {
        container.innerHTML = `
            <div class="col-span-full bg-white rounded-3xl p-10 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-box-open text-4xl mb-2"></i>
                <p class="text-sm font-semibold">Tidak ada produk yang tersedia pada kategori ini.</p>
            </div>
        `;
        let pagContainer = document.getElementById('pos_pagination_container');
        if (pagContainer) pagContainer.classList.add('hidden');
        return;
    }

    if (products && products.length > 0) {
        products.forEach(product => {
            let productType = (product.type || 'physical').toLowerCase().trim();
            let isDigital = (productType === 'digital');
            let actualStock = parseInt(product.current_stock !== undefined ? product.current_stock : product.stock) || 0;
            let minStock = parseInt(product.min_stock) || 0;
            let isOutOfStock = !isDigital && (actualStock <= 0);

            let formattedPrice = new Intl.NumberFormat('id-ID').format(product.selling_price || 0);

            let badgeClass = isDigital ? 'bg-blue-100 text-blue-700' : (isOutOfStock ? 'bg-rose-100 text-rose-700' : 'bg-green-100 text-green-700');
            let stockTextClass = isOutOfStock ? 'text-rose-600 font-black' : (actualStock <= minStock ? 'text-amber-600 font-bold animate-pulse' : 'text-gray-500');
            let cardBorderClass = isOutOfStock ? 'opacity-60 bg-gray-50 border-rose-200 cursor-not-allowed' : 'hover:border-indigo-500 cursor-pointer active:scale-98 bg-white';

            let clickAttr = !isOutOfStock ? `onclick='openAddToCartModal(${JSON.stringify(product)})'` : '';

            let buttonHTML = isOutOfStock ? `
                <button type="button" disabled class="w-full bg-gray-200 text-gray-400 text-xs font-bold py-2.5 rounded-xl flex items-center justify-center space-x-1.5 cursor-not-allowed">
                    <i class="fa-solid fa-ban text-xs"></i>
                    <span>Stok Habis</span>
                </button>
            ` : `
                <button type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm active:scale-95">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah</span>
                </button>
            `;

            let stockBadgeHTML = !isDigital ? `
                <span class="text-[11px] font-semibold ${stockTextClass}">
                    ${isOutOfStock ? 'Stok Habis' : 'Stok: ' + actualStock}
                </span>
            ` : '';

            let cardHTML = `
                <div class="product-item cat-${product.category_id} rounded-2xl shadow-sm border border-gray-200/90 p-3.5 flex flex-col justify-between transition group ${cardBorderClass}"
                     ${clickAttr}
                     data-name="${(product.name || '').toLowerCase()}"
                     data-code="${(product.code || '').toLowerCase()}"
                     data-price="${product.selling_price}">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full ${badgeClass}">
                                ${isDigital ? 'Digital' : 'Fisik'}
                            </span>
                            ${stockBadgeHTML}
                        </div>

                        <h4 class="font-bold text-gray-800 text-xs md:text-sm line-clamp-2 mb-1 group-hover:text-indigo-600 transition leading-snug">
                            ${product.name}
                        </h4>
                        <p class="text-[11px] text-gray-400 mb-2 font-mono">${product.code || '-'}</p>
                    </div>

                    <div>
                        <div class="text-sm md:text-base font-black text-indigo-700 mb-2">
                            Rp ${formattedPrice}
                        </div>
                        ${buttonHTML}
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', cardHTML);
        });
    }

    applyProductFilters();
}

// 8. LOGIKA MUNDUR BERTAHAP SAAT KLIK TOMBOL KEMBALI
function resetCategoryNavigation() {
    // JIKA BERADA DI DAFTAR PRODUK HASIL PILIHAN SUB-DETAIL (LEVEL 4) -> MUNDUR KE PILIHAN SUB-DETAIL LEVEL 3
    if (navLevel === 4 && currentSubDetailKey !== '') {
        navigateToSubDetail(currentSubDetailKey);
        return;
    }

    // JIKA BERADA DI PILIHAN SUB-DETAIL LEVEL 3 -> MUNDUR KE SUB AKSESORIS LEVEL 2
    if (navLevel === 3 && currentSubDetailKey !== '') {
        navigateToSubCategory('aksesoris', 'Sub Aksesoris');
        return;
    }

    // JIKA BERADA DI GRID PRODUK HASIL LEVEL 2 (PULSA, HP, EWALLET, BANK, ATAU AKSESORIS UTAMA) -> MUNDUR KE LEVEL 2
    if (navLevel === 3 && ['pulsa', 'voucher', 'perdana', 'kartu-perdana', 'handphone', 'hp', 'ewallet', 'bank', 'transfer', 'transfer-bank', 'aksesoris', 'aksesoris-hp'].includes(selectedCategory)) {
        navLevel = 2;
        selectedProvider = '';
        currentSubDetailKey = '';
        window.selectedProviderTitle = '';
        
        document.getElementById('view_products_grid').classList.add('hidden');
        document.getElementById('view_sub_providers').classList.remove('hidden');
        
        togglePriceFilterVisibility(false);
        
        let titleText = document.getElementById('catalog_title_text');
        if (titleText) {
            let labelTitle = 'Operator / Provider';
            if (selectedCategory.includes('handphone') || selectedCategory === 'hp') labelTitle = 'Tipe Handphone';
            else if (selectedCategory.includes('aksesoris')) labelTitle = 'Sub Aksesoris';
            else if (selectedCategory.includes('ewallet') || selectedCategory.includes('wallet')) labelTitle = 'E-Wallet';
            else if (selectedCategory.includes('bank') || selectedCategory.includes('transfer')) labelTitle = 'Bank';

            titleText.innerHTML = '<i class="fa-solid fa-list mr-2 text-indigo-600"></i> Pilih ' + labelTitle;
        }

        let grids = [
            'sub_cellular_grid', 'sub_ewallet_grid', 'sub_bank_grid', 
            'sub_handphone_grid', 'sub_aksesoris_grid', 'sub_proteksi_grid', 
            'sub_power_grid', 'sub_audio_grid', 'sub_penyimpanan_grid', 'sub_mount_grid'
        ];
        grids.forEach(g => {
            let el = document.getElementById(g);
            if (el) el.classList.add('hidden');
        });

        if (selectedCategory.includes('handphone') || selectedCategory === 'hp') {
            let el = document.getElementById('sub_handphone_grid');
            if (el) el.classList.remove('hidden');
        } else if (selectedCategory.includes('ewallet') || selectedCategory.includes('wallet')) {
            let el = document.getElementById('sub_ewallet_grid');
            if (el) el.classList.remove('hidden');
        } else if (selectedCategory.includes('bank') || selectedCategory.includes('transfer')) {
            let el = document.getElementById('sub_bank_grid');
            if (el) el.classList.remove('hidden');
        } else if (selectedCategory.includes('aksesoris')) {
            let el = document.getElementById('sub_aksesoris_grid');
            if (el) el.classList.remove('hidden');
        } else {
            let el = document.getElementById('sub_cellular_grid');
            if (el) el.classList.remove('hidden');
        }

        return;
    }

    // MUNDUR KE KATALOG UTAMA (LEVEL 1)
    navLevel = 1;
    selectedCategory = '';
    selectedProvider = '';
    currentSubDetailKey = '';
    window.selectedProviderTitle = '';
    
    document.getElementById('view_main_categories').classList.remove('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.add('hidden');
    
    togglePriceFilterVisibility(false);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.add('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Katalog Utama';
    }
}

function formatPriceInput(input) {
    let rawValue = input.value.replace(/\D/g, '');
    if (rawValue === '') {
        input.value = '';
    } else {
        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }
    applyProductFilters();
}

function applyProductFilters() {
    let searchInput = document.getElementById('search_product');
    let searchKeyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    let minPriceInput = document.getElementById('filter_min_price');
    let maxPriceInput = document.getElementById('filter_max_price');
    
    let minPrice = minPriceInput && minPriceInput.value !== '' ? parseFloat(minPriceInput.value.replace(/\./g, '')) : 0;
    let maxPrice = maxPriceInput && maxPriceInput.value !== '' ? parseFloat(maxPriceInput.value.replace(/\./g, '')) : Infinity;

    let items = document.querySelectorAll('.product-item');
    posVisibleProducts = [];

    items.forEach(item => {
        let name = (item.getAttribute('data-name') || '').toLowerCase();
        let code = (item.getAttribute('data-code') || '').toLowerCase();
        let price = parseFloat(item.getAttribute('data-price')) || 0;

        let matchSearch = searchKeyword === '' || name.includes(searchKeyword) || code.includes(searchKeyword);
        let matchPrice = (price >= minPrice && price <= maxPrice);

        if (matchSearch && matchPrice) {
            posVisibleProducts.push(item);
        } else {
            item.classList.add('hidden');
        }
    });

    let selectEl = document.getElementById('pos_items_per_page');
    if (selectEl) {
        posItemsPerPage = parseInt(selectEl.value) || 10;
    }

    posCurrentPage = 1;
    renderPosPagination();
}

function renderPosPagination() {
    let pagContainer = document.getElementById('pos_pagination_container');
    if (!pagContainer) return;

    let totalItems = posVisibleProducts.length;

    if (totalItems === 0) {
        pagContainer.classList.add('hidden');
        return;
    }

    pagContainer.classList.remove('hidden');

    let totalPages = Math.ceil(totalItems / posItemsPerPage) || 1;
    if (posCurrentPage > totalPages) posCurrentPage = totalPages;

    let start = (posCurrentPage - 1) * posItemsPerPage;
    let end = start + posItemsPerPage;

    document.querySelectorAll('.product-item').forEach(item => item.classList.add('hidden'));

    posVisibleProducts.slice(start, end).forEach(item => item.classList.remove('hidden'));

    let infoEl = document.getElementById('pos_pagination_info');
    if (infoEl) {
        let showingStart = start + 1;
        let showingEnd = Math.min(end, totalItems);
        infoEl.innerText = `Menampilkan ${showingStart} - ${showingEnd} dari ${totalItems} produk`;
    }

    let btnContainer = document.getElementById('pos_pagination_buttons');
    if (!btnContainer) return;

    btnContainer.innerHTML = '';

    let prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.disabled = posCurrentPage === 1;
    prevBtn.onclick = () => goToPosPage(posCurrentPage - 1);
    prevBtn.className = `px-2.5 py-1.5 rounded-xl border border-gray-200 text-xs font-bold transition cursor-pointer ${posCurrentPage === 1 ? 'opacity-40 cursor-not-allowed bg-gray-50 text-gray-400' : 'bg-white hover:bg-gray-50 text-gray-700'}`;
    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
    btnContainer.appendChild(prevBtn);

    for (let i = 1; i <= totalPages; i++) {
        if (totalPages > 5 && (i < posCurrentPage - 1 || i > posCurrentPage + 1) && i !== 1 && i !== totalPages) {
            if (i === posCurrentPage - 2 || i === posCurrentPage + 2) {
                let dots = document.createElement('span');
                dots.className = 'px-1 text-xs text-gray-400';
                dots.innerText = '...';
                btnContainer.appendChild(dots);
            }
            continue;
        }

        let pageBtn = document.createElement('button');
        pageBtn.type = 'button';
        pageBtn.onclick = () => goToPosPage(i);
        pageBtn.className = `px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer ${i === posCurrentPage ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-gray-200 hover:bg-gray-50 text-gray-700'}`;
        pageBtn.innerText = i;
        btnContainer.appendChild(pageBtn);
    }

    let nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.disabled = posCurrentPage === totalPages || totalItems === 0;
    nextBtn.onclick = () => goToPosPage(posCurrentPage + 1);
    nextBtn.className = `px-2.5 py-1.5 rounded-xl border border-gray-200 text-xs font-bold transition cursor-pointer ${(posCurrentPage === totalPages || totalItems === 0) ? 'opacity-40 cursor-not-allowed bg-gray-50 text-gray-400' : 'bg-white hover:bg-gray-50 text-gray-700'}`;
    nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
    btnContainer.appendChild(nextBtn);
}

function goToPosPage(page) {
    posCurrentPage = page;
    renderPosPagination();
}

function changePosPerPage() {
    let selectEl = document.getElementById('pos_items_per_page');
    posItemsPerPage = parseInt(selectEl ? selectEl.value : 10) || 10;
    posCurrentPage = 1;
    renderPosPagination();
}

function filterProducts(event) {
    applyProductFilters();
}