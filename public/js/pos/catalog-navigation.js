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
            // Kosongkan nilai input saat disembunyikan/reset
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
    
    // Di Level 2 (pilih provider), filter harga belum ditampilkan
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

// 2. DARI LEVEL 2 KE LEVEL 3 (TAMPILKAN PRODUK SELEPAS FILTER PROVIDER / SUB-AKSESORIS)
function selectProviderFilter(providerKey, title) {
    navLevel = 3;
    selectedProvider = providerKey.toLowerCase();

    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    // Masuk ke grid produk, tampilkan filter harga
    togglePriceFilterVisibility(true);
    
    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Produk ' + title;
    }

    applyProductFilters();
}

// 3. LANGSUNG KE PRODUK DARI LEVEL 1 (PLN TERPISAH)
function navigateToDirectCategory(slugKey, title) {
    navLevel = 3;
    selectedCategory = slugKey.toLowerCase();
    selectedProvider = '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    // Masuk ke grid produk langsung, tampilkan filter harga
    togglePriceFilterVisibility(true);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> ' + title;
    }

    applyProductFilters();
}

// 4. TAMPILKAN SEMUA PRODUK
function showAllProducts() {
    navLevel = 3;
    selectedCategory = 'all';
    selectedProvider = '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    // Masuk ke grid semua produk, tampilkan filter harga
    togglePriceFilterVisibility(true);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Semua Produk';
    }

    applyProductFilters();
}

// 5. TOMBOL KEMBALI HIERARKI NAVIGASI
function resetCategoryNavigation() {
    let hasSubLevel = ['pulsa', 'voucher', 'perdana', 'kartu-perdana', 'ewallet', 'bank', 'transfer-bank', 'aksesoris', 'aksesoris-hp'].includes(selectedCategory);

    if (navLevel === 3 && hasSubLevel) {
        navLevel = 2;
        selectedProvider = '';
        
        document.getElementById('view_products_grid').classList.add('hidden');
        document.getElementById('view_sub_providers').classList.remove('hidden');
        
        // Kembali ke Level 2 (pilih sub-kategori), sembunyikan filter harga
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
        
        // Kembali ke Level 1 (Katalog Utama), sembunyikan filter harga
        togglePriceFilterVisibility(false);
        
        let backBtn = document.getElementById('btn_back_category');
        if (backBtn) backBtn.classList.add('hidden');

        let titleText = document.getElementById('catalog_title_text');
        if (titleText) {
            titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Katalog Utama';
        }
    }
}

// 6. FUNGSI FORMAT INPUT HARGA OTOMATIS (PISAH TITIK RIBUAN)
function formatPriceInput(input) {
    let rawValue = input.value.replace(/\D/g, ''); // Ambil angka saja
    if (rawValue === '') {
        input.value = '';
    } else {
        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }
    filterProducts(); // Jalankan filter realtime otomatis
}

// 7. PENYARINGAN KARTU PRODUK (KATEGORI, PROVIDER, SEARCH & RENTANG HARGA)
function applyProductFilters() {
    let searchInput = document.getElementById('search_product');
    let searchKeyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    let minPriceInput = document.getElementById('filter_min_price');
    let maxPriceInput = document.getElementById('filter_max_price');
    
    // Bersihkan titik sebelum di-parse ke float
    let minPrice = minPriceInput && minPriceInput.value !== '' ? parseFloat(minPriceInput.value.replace(/\./g, '')) : 0;
    let maxPrice = maxPriceInput && maxPriceInput.value !== '' ? parseFloat(maxPriceInput.value.replace(/\./g, '')) : Infinity;

    let items = document.querySelectorAll('.product-item');
    let customCard = document.getElementById('card_custom_amount');

    if (customCard) {
        if (selectedCategory === 'bank' || selectedCategory === 'transfer-bank' || selectedCategory === 'ewallet') {
            customCard.classList.remove('hidden');
        } else {
            customCard.classList.add('hidden');
        }
    }

    items.forEach(item => {
        let cat = (item.getAttribute('data-category') || '').toLowerCase();
        let name = (item.getAttribute('data-name') || '').toLowerCase();
        let code = (item.getAttribute('data-code') || '').toLowerCase();
        let price = parseFloat(item.getAttribute('data-price')) || 0;

        let matchCat = false;
        if (selectedCategory === '' || selectedCategory === 'all') {
            matchCat = true;
        } else if (selectedCategory === 'perdana' || selectedCategory === 'kartu-perdana') {
            matchCat = cat.includes('perdana') || cat.includes('kartu') || name.includes('perdana');
        } else if (selectedCategory === 'ewallet') {
            matchCat = cat.includes('ewallet') || cat.includes('wallet') || cat.includes('topup');
        } else if (selectedCategory === 'bank' || selectedCategory === 'transfer-bank' || selectedCategory === 'transfer') {
            matchCat = cat.includes('bank') || cat.includes('transfer') || cat.includes('kirim');
        } else if (selectedCategory === 'token-pln' || selectedCategory === 'pln') {
            matchCat = cat.includes('pln') || cat.includes('token');
        } else if (selectedCategory === 'aksesoris-hp' || selectedCategory === 'aksesoris') {
            matchCat = cat.includes('aksesoris') || cat.includes('acc') || cat.includes('proteksi') || cat.includes('power') || cat.includes('audio') || cat.includes('penyimpanan') || cat.includes('mount');
        } else {
            matchCat = cat.includes(selectedCategory);
        }

        let matchProv = true;
        if (selectedProvider !== '') {
            let provKey = selectedProvider.toLowerCase().trim();

            // PENCATATAN ALIAS KHUSUS OPERATOR TRI / THREE / 3
            if (provKey === 'tri' || provKey === 'three' || provKey === '3') {
                matchProv = cat.includes('tri') || cat.includes('three') || cat.includes('3') ||
                            name.includes('tri') || name.includes('three') || name.includes(' 3 ') || name.includes('3gb') || name.includes('3h') || name.includes('happy') || name.includes('aon') ||
                            code.includes('v-3-') || code.includes('tri') || code.includes('three');
            } 
            // PENCATATAN ALIAS KHUSUS OPERATOR TELKOMSEL / TSEL / BYU
            else if (provKey === 'telkomsel' || provKey === 'tsel') {
                matchProv = cat.includes('telkomsel') || cat.includes('tsel') || cat.includes('byu') ||
                            name.includes('telkomsel') || name.includes('by.u') || name.includes('tsel') ||
                            code.includes('tsel') || code.includes('byu');
            } 
            // PENCATATAN ALIAS KHUSUS OPERATOR INDOSAT / ISAT / IM3
            else if (provKey === 'indosat' || provKey === 'isat' || provKey === 'im3') {
                matchProv = cat.includes('indosat') || cat.includes('isat') || cat.includes('im3') ||
                            name.includes('indosat') || name.includes('im3') ||
                            code.includes('isat') || code.includes('indosat');
            } 
            else {
                matchProv = cat.includes(provKey) || name.includes(provKey) || code.includes(provKey);
            }
        }

        let matchSearch = true;
        if (searchKeyword !== '') {
            matchSearch = name.includes(searchKeyword) || code.includes(searchKeyword) || cat.includes(searchKeyword);
        }

        let matchPrice = (price >= minPrice && price <= maxPrice);

        if (matchCat && matchProv && matchSearch && matchPrice) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

// 8. PENCARIAN REALTIME & SERVER-SIDE FALLBACK
function filterProducts(event) {
    let searchInput = document.getElementById('search_product');
    let searchKeyword = searchInput ? searchInput.value.trim() : '';
    
    let minPriceInput = document.getElementById('filter_min_price');
    let maxPriceInput = document.getElementById('filter_max_price');
    let hasPriceFilter = (minPriceInput && minPriceInput.value !== '') || (maxPriceInput && maxPriceInput.value !== '');

    // Jika pengguna menekan tombol Enter pada input pencarian, kirim request pencarian ke Server
    if (event && event.key === 'Enter') {
        let form = searchInput.closest('form');
        if (form) {
            form.submit();
            return;
        }
    }

    if ((searchKeyword.length > 0 || hasPriceFilter) && navLevel !== 3) {
        navLevel = 3;
        selectedCategory = 'all';
        selectedProvider = '';
        
        let mainCat = document.getElementById('view_main_categories');
        let subProv = document.getElementById('view_sub_providers');
        let prodGrid = document.getElementById('view_products_grid');

        if (mainCat) mainCat.classList.add('hidden');
        if (subProv) subProv.classList.add('hidden');
        if (prodGrid) prodGrid.classList.remove('hidden');
        
        togglePriceFilterVisibility(true);
        
        let backBtn = document.getElementById('btn_back_category');
        if (backBtn) backBtn.classList.remove('hidden');
    }

    applyProductFilters();
}