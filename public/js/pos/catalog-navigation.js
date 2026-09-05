// VARIABLE GLOBAL NAVIGASI KATALOG BERTINGKAT
let navLevel = 1;
let selectedCategory = '';
let selectedProvider = '';

// Helper untuk mengatur visibilitas kontainer filter harga
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

// 1. DARI LEVEL 1 KE LEVEL 2 (PILIH PROVIDER / SUB-KATEGORI UTAMA)
function navigateToSubCategory(catType, title) {
    navLevel = 2;
    selectedCategory = catType.toLowerCase();

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

    let cellGrid = document.getElementById('sub_cellular_grid');
    let walletGrid = document.getElementById('sub_ewallet_grid');
    let bankGrid = document.getElementById('sub_bank_grid');
    let aksesorisGrid = document.getElementById('sub_aksesoris_grid');

    if (cellGrid) cellGrid.classList.add('hidden');
    if (walletGrid) walletGrid.classList.add('hidden');
    if (bankGrid) bankGrid.classList.add('hidden');
    if (aksesorisGrid) aksesorisGrid.classList.add('hidden');

    let normalizedType = catType.toLowerCase();
    
    if (normalizedType === 'ewallet') {
        if (walletGrid) walletGrid.classList.remove('hidden');
    } else if (normalizedType === 'bank' || normalizedType === 'transfer-bank' || normalizedType === 'transfer') {
        if (bankGrid) bankGrid.classList.remove('hidden');
    } else if (normalizedType === 'aksesoris' || normalizedType === 'aksesoris-hp') {
        if (aksesorisGrid) aksesorisGrid.classList.remove('hidden');
    } else {
        if (cellGrid) cellGrid.classList.remove('hidden');
    }
}

// 2. DARI LEVEL 2 KE LEVEL 3 (LOAD AJAX PRODUK BERDASARKAN PROVIDER / SUB-KATEGORI)
function selectProviderFilter(providerKey, title) {
    navLevel = 3;
    selectedProvider = providerKey.toLowerCase();

    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    togglePriceFilterVisibility(true);
    
    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Produk ' + title;
    }

    fetchProductsFromServer(selectedCategory, selectedProvider);
}

// 3. LANGSUNG KE PRODUK DARI LEVEL 1 (PLN TERPISAH)
function navigateToDirectCategory(slugKey, title) {
    navLevel = 3;
    selectedCategory = slugKey.toLowerCase();
    selectedProvider = '';

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

// 4. TAMPILKAN SEMUA PRODUK (ON-DEMAND)
function showAllProducts() {
    navLevel = 3;
    selectedCategory = 'all';
    selectedProvider = '';

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

// 5. FUNGSI FETCH AJAX DARI SERVER & RENDER GRID
function fetchProductsFromServer(category, provider) {
    let gridView = document.getElementById('view_products_grid');
    if (!gridView) return;

    // Ambil elemen pembungkus grid di dalam view_products_grid
    let container = gridView.querySelector('.grid');
    if (!container) return;

    // Tampilkan Indikator Loading
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

// 6. FUNGSI MERENDER ITEM PRODUK MENJADI CARD HTML
function renderProductsHTML(products, container) {
    container.innerHTML = ''; // Bersihkan tampilan loading

    if (!products || products.length === 0) {
        container.innerHTML = `
            <div class="col-span-full bg-white rounded-3xl p-10 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-box-open text-4xl mb-2"></i>
                <p class="text-sm font-semibold">Tidak ada produk yang tersedia pada kategori ini.</p>
            </div>
        `;
        return;
    }

    products.forEach(product => {
        let productType = (product.type || 'physical').toLowerCase().trim();
        let isDigital = (productType === 'digital');
        let actualStock = parseInt(product.current_stock !== undefined ? product.current_stock : product.stock) || 0;
        let minStock = parseInt(product.min_stock) || 0;
        let isOutOfStock = !isDigital && (actualStock <= 0);

        let formattedPrice = new Intl.NumberFormat('id-ID').format(product.selling_price || 0);

        // Class styling
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

    applyProductFilters(); // Terapkan filter harga atau pencarian jika ada yang diketik
}

// 7. TOMBOL KEMBALI HIERARKI NAVIGASI
function resetCategoryNavigation() {
    let hasSubLevel = ['pulsa', 'voucher', 'perdana', 'kartu-perdana', 'ewallet', 'bank', 'transfer-bank', 'aksesoris', 'aksesoris-hp'].includes(selectedCategory);

    if (navLevel === 3 && hasSubLevel) {
        navLevel = 2;
        selectedProvider = '';
        
        document.getElementById('view_products_grid').classList.add('hidden');
        document.getElementById('view_sub_providers').classList.remove('hidden');
        
        togglePriceFilterVisibility(false);
        
        let titleText = document.getElementById('catalog_title_text');
        if (titleText) {
            let labelTitle = 'Operator';
            if (selectedCategory === 'aksesoris' || selectedCategory === 'aksesoris-hp') labelTitle = 'Sub Aksesoris';
            else if (selectedCategory === 'ewallet') labelTitle = 'E-Wallet';
            else if (selectedCategory === 'bank' || selectedCategory === 'transfer-bank') labelTitle = 'Bank';

            titleText.innerHTML = '<i class="fa-solid fa-list mr-2 text-indigo-600"></i> Pilih ' + labelTitle;
        }
    } else {
        navLevel = 1;
        selectedCategory = '';
        selectedProvider = '';
        
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
}

// 8. FUNGSI FORMAT INPUT HARGA OTOMATIS
function formatPriceInput(input) {
    let rawValue = input.value.replace(/\D/g, '');
    if (rawValue === '') {
        input.value = '';
    } else {
        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }
    applyProductFilters();
}

// 9. PENYARINGAN KARTU PRODUK REALTIME (SEARCH LOCAL & HARGA)
function applyProductFilters() {
    let searchInput = document.getElementById('search_product');
    let searchKeyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    let minPriceInput = document.getElementById('filter_min_price');
    let maxPriceInput = document.getElementById('filter_max_price');
    
    let minPrice = minPriceInput && minPriceInput.value !== '' ? parseFloat(minPriceInput.value.replace(/\./g, '')) : 0;
    let maxPrice = maxPriceInput && maxPriceInput.value !== '' ? parseFloat(maxPriceInput.value.replace(/\./g, '')) : Infinity;

    let items = document.querySelectorAll('.product-item');

    items.forEach(item => {
        let name = (item.getAttribute('data-name') || '').toLowerCase();
        let code = (item.getAttribute('data-code') || '').toLowerCase();
        let price = parseFloat(item.getAttribute('data-price')) || 0;

        let matchSearch = searchKeyword === '' || name.includes(searchKeyword) || code.includes(searchKeyword);
        let matchPrice = (price >= minPrice && price <= maxPrice);

        if (matchSearch && matchPrice) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

// 10. EVENT PENCARIAN REALTIME
function filterProducts(event) {
    applyProductFilters();
}