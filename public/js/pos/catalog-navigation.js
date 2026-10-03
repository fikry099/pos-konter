// ==========================================
// VARIABLE GLOBAL NAVIGASI KATALOG BERTINGKAT
// ==========================================
let navLevel = 1;
let selectedCategory = '';
let selectedProvider = '';
let selectedBrand = 'all'; // Menyimpan filter merek aktif
window.selectedProviderTitle = '';

let posCurrentPage = 1;
let posItemsPerPage = 12;
let posVisibleProducts = [];

// ==========================================
// DAFTAR MEREK KHUSUS AKSESORIS
// ==========================================
const CABLE_DATA_BRANDS = [
    { key: 'vivan', name: 'Vivan' },
    { key: 'robot', name: 'Robot' },
    { key: 'log-on', name: 'Log-on' },
    { key: 'oraimo', name: 'Oraimo' },
    { key: 'roket', name: 'Rocket' },
    { key: 'olike', name: 'Olike' },
    { key: 'tecnix', name: 'Tecnix' },
    { key: 'rapa', name: 'Rapa' },
    { key: 'luna', name: 'Luna' }
];

const CHARGER_SET_BRANDS = [
    { key: 'xiaomi', name: 'Xiaomi / Mi' },
    { key: 'wellcomm', name: 'Wellcomm' },
    { key: 'hikaru', name: 'Hikaru' },
    { key: 'dap', name: 'DAP' },
    { key: 'oraimo', name: 'Oraimo' },
    { key: 'log-on', name: 'Log-on' },
    { key: 'rapa', name: 'Rapa' },
    { key: 'olike', name: 'Olike' },
    { key: 'tecno', name: 'Tecno' }
];

const ADAPTER_HEAD_BRANDS = [
    { key: 'roket', name: 'Rocket' },
    { key: 'olike', name: 'Olike' },
    { key: 'wellcomm', name: 'Wellcomm' },
    { key: 'vivan', name: 'Vivan' },
    { key: 'robot', name: 'Robot' },
    { key: 'oraimo', name: 'Oraimo' },
    { key: 'samsung', name: 'Samsung' }
];

const HEADSET_EARPHONE_BRANDS = [
    { key: 'robot', name: 'Robot' },
    { key: 'oraimo', name: 'Oraimo' },
    { key: 'olike', name: 'Olike' },
    { key: 'wellcomm', name: 'Wellcomm' },
    { key: 'vivan', name: 'Vivan' },
    { key: 'tecnix', name: 'Tecnix' },
    { key: 'dap', name: 'DAP' },
    { key: 'sony', name: 'Sony' },
    { key: 'philips', name: 'Philips' },
    { key: 'log-on', name: 'Log-On' }
];

const STORAGE_BRANDS = [
    { key: 'robot', name: 'Robot' },
    { key: 'vivan', name: 'Vivan' },
    { key: 'olike', name: 'Olike' },
    { key: 'toshiba', name: 'Toshiba' },
    { key: 'v-gen', name: 'V-Gen' }
];

const POWERBANK_BRANDS = [
    { key: 'oraimo', name: 'Oraimo' },
    { key: 'tecnix', name: 'Tecnix' },
    { key: 'veger', name: 'Veger' },
    { key: 'vizz', name: 'Vizz' },
    { key: 'boldwave', name: 'Boldwave' },
    { key: 'nicol', name: 'Nicol' }
];

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

// 1. DARI LEVEL 1 KE LEVEL 2 (SUB-KATEGORI / PROVIDER)
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

    let grids = [
        'sub_cellular_grid', 
        'sub_ewallet_grid', 
        'sub_bank_grid', 
        'sub_handphone_grid', 
        'sub_aksesoris_grid'
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

// 2. SELEKSI SUB-ITEM / PROVIDER UNTUK MENGAMBIL DAFTAR PRODUK (LEVEL 2 KE LEVEL 3)
function selectProviderFilter(providerKey, title) {
    let catLower = (selectedCategory || '').toLowerCase();
    let provLower = (providerKey || '').toLowerCase();

    // PENGECEKAN APAKAH TARGET MERUPAKAN BANK, E-WALLET, ATAU PROVIDER PULSA SELULER
    let isBankOrWalletTarget = (
        catLower.includes('wallet') || catLower.includes('ewallet') || 
        catLower.includes('bank') || catLower.includes('transfer') ||
        ['dana', 'gopay', 'ovo', 'shopee', 'shopeepay', 'linkaja', 'maxim', 
         'bca', 'bri', 'mandiri', 'bni', 'bsi', 'seabank', 'jago', 'cimb', 'permata', 'danamon', 'btn', 'bpd', 'lainnya'].includes(provLower)
    ) && !provLower.includes('powerbank') && provLower !== 'pb';

    let isPulsaTarget = catLower.includes('pulsa') || catLower.includes('cellular') || catLower.includes('seluler') ||
                        ['tsel', 'telkomsel', 'isat', 'indosat', 'tri', 'three', '3', 'xl', 'axis', 'smartfren', 'smart', 'by.u'].includes(provLower);

    // JIKA YANG DIKLIK ADALAH BANK, E-WALLET, ATAU PROVIDER PULSA -> LANGSUNG BUKA MODAL NOMINAL BEBAS
    if (isBankOrWalletTarget || isPulsaTarget) {
        let displayTitle = title || providerKey;
        if ((catLower.includes('bank') || catLower.includes('transfer')) && !displayTitle.toLowerCase().includes('bank')) {
            displayTitle = 'Bank ' + displayTitle;
        } else if (isPulsaTarget && !displayTitle.toLowerCase().includes('pulsa')) {
            displayTitle = 'Pulsa ' + displayTitle;
        }
        
        // Simpan title provider aktif
        window.selectedProviderTitle = displayTitle;
        
        // Buka modal input nominal kustom secara otomatis
        openCustomAmountModal(displayTitle);
        return;
    }

    navLevel = 3;
    selectedProvider = providerKey.toLowerCase();
    selectedBrand = 'all';
    window.selectedProviderTitle = title || providerKey;

    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    let gridView = document.getElementById('view_products_grid');
    if (gridView) {
        let container = gridView.querySelector('.grid');
        if (container) {
            container.innerHTML = `
                <div class="col-span-full bg-white rounded-3xl p-10 text-center text-indigo-600 border border-indigo-100 shadow-sm">
                    <i class="fa-solid fa-circle-notch fa-spin text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-slate-700">Memuat produk ${title || providerKey}...</p>
                </div>
            `;
        }
    }

    let brandSidebar = document.getElementById('brand_sidebar_container');
    let isPowerbank   = provLower.includes('powerbank') || provLower.includes('pb');
    let isStorage     = provLower.includes('mmc') || provLower.includes('flashdisk') || provLower.includes('memori') || provLower.includes('memory') || provLower.includes('fd');
    let isHeadset     = provLower.includes('headset') || provLower.includes('earphone') || provLower.includes('tws') || provLower.includes('hf') || provLower.includes('handsfree');
    let isCableData   = provLower.includes('cable') || provLower.includes('kabel') || provLower.includes('aux');
    let isChargerSet  = (provLower.includes('charger') && provLower.includes('set')) || provLower.includes('car-charger') || provLower.includes('adaptor-set');
    let isAdapterHead = provLower.includes('adaptor-kepala') || provLower.includes('kepala') || provLower === 'adaptor' || (provLower.includes('charger') && !provLower.includes('set'));

    if (brandSidebar) {
        if (isPowerbank || isStorage || isHeadset || isCableData || isChargerSet || isAdapterHead || catLower.includes('aksesoris')) {
            brandSidebar.classList.remove('hidden');
            renderDynamicBrandList(selectedCategory, selectedProvider);
        } else {
            brandSidebar.classList.add('hidden');
        }
    }

    togglePriceFilterVisibility(true);
    
    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Produk ' + title;
    }

    fetchProductsFromServer(selectedCategory, selectedProvider);
}

function renderDynamicBrandList(category, provider) {
    let provLower = (provider || '').toLowerCase();
    let wrapper = document.getElementById('brand_list_wrapper');
    if (!wrapper) return;

    let isPowerbank   = provLower.includes('powerbank') || provLower.includes('pb');
    let isStorage     = provLower.includes('mmc') || provLower.includes('flashdisk') || provLower.includes('memori') || provLower.includes('memory') || provLower.includes('fd');
    let isHeadset     = provLower.includes('headset') || provLower.includes('earphone') || provLower.includes('tws') || provLower.includes('hf') || provLower.includes('handsfree');
    let isCableData   = provLower.includes('cable') || provLower.includes('kabel') || provLower.includes('aux');
    let isChargerSet  = (provLower.includes('charger') && provLower.includes('set')) || provLower.includes('car-charger') || provLower.includes('adaptor-set');
    let isAdapterHead = provLower.includes('adaptor-kepala') || provLower.includes('kepala') || provLower === 'adaptor' || (provLower.includes('charger') && !provLower.includes('set'));

    let brandArray = [];
    if (isPowerbank) {
        brandArray = POWERBANK_BRANDS;
    } else if (isStorage) {
        brandArray = STORAGE_BRANDS;
    } else if (isHeadset) {
        brandArray = HEADSET_EARPHONE_BRANDS;
    } else if (isCableData) {
        brandArray = CABLE_DATA_BRANDS;
    } else if (isChargerSet) {
        brandArray = CHARGER_SET_BRANDS;
    } else if (isAdapterHead) {
        brandArray = ADAPTER_HEAD_BRANDS;
    }

    let countBadge = document.getElementById('brand_count_badge');
    if (countBadge) {
        countBadge.innerText = brandArray.length;
    }

    if (brandArray.length > 0) {
        let html = `
            <button type="button" 
                    onclick="selectBrandFilter('all')" 
                    data-brand-btn="all"
                    class="brand-filter-btn shrink-0 text-left px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-bold transition flex items-center justify-between space-x-2">
                <span class="whitespace-nowrap">Semua Merek</span>
                <i class="fa-solid fa-check text-[10px]"></i>
            </button>
        `;

        brandArray.forEach(b => {
            html += `
                <button type="button" 
                        onclick="selectBrandFilter('${b.key}')" 
                        data-brand-btn="${b.key}"
                        class="brand-filter-btn shrink-0 text-left px-3.5 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-bold transition flex items-center justify-between whitespace-nowrap">
                    <span>${b.name}</span>
                </button>
            `;
        });

        wrapper.innerHTML = html;
    } else {
        resetBrandSidebarUI();
    }

    let searchInput = document.getElementById('search_brand_input');
    if (searchInput) searchInput.value = '';
    filterBrandList();
}

function navigateToDirectCategory(slugKey, title) {
    navLevel = 3;
    selectedCategory = slugKey.toLowerCase();
    selectedProvider = '';
    selectedBrand = 'all';
    window.selectedProviderTitle = title || '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    let brandSidebar = document.getElementById('brand_sidebar_container');
    if (brandSidebar) brandSidebar.classList.add('hidden');

    togglePriceFilterVisibility(true);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> ' + title;
    }

    fetchProductsFromServer(selectedCategory, '');
}

function showAllProducts() {
    navLevel = 3;
    selectedCategory = 'all';
    selectedProvider = '';
    selectedBrand = 'all';
    window.selectedProviderTitle = '';

    document.getElementById('view_main_categories').classList.add('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.remove('hidden');
    
    let brandSidebar = document.getElementById('brand_sidebar_container');
    if (brandSidebar) brandSidebar.classList.add('hidden');

    togglePriceFilterVisibility(true);
    
    let backBtn = document.getElementById('btn_back_category');
    if (backBtn) backBtn.classList.remove('hidden');

    let titleText = document.getElementById('catalog_title_text');
    if (titleText) {
        titleText.innerHTML = '<i class="fa-solid fa-boxes-stacked mr-2 text-indigo-600"></i> Semua Produk';
    }

    fetchProductsFromServer('all', '');
}

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

// 6. RENDER ITEM PRODUK KETIKA DILOAD
function renderProductsHTML(products, container) {
    container.innerHTML = '';

    let catStr = (selectedCategory || '').toLowerCase();
    let provStr = (selectedProvider || '').toLowerCase();
    
    let isBankOrWallet = (
        catStr.includes('bank') || catStr.includes('ewallet') || catStr.includes('wallet') || catStr.includes('transfer') ||
        provStr.includes('bank') || provStr.includes('ewallet') || provStr.includes('bca') || provStr.includes('bri') || provStr.includes('bni') || provStr.includes('mandiri') || provStr.includes('dana') || provStr.includes('ovo') || provStr.includes('gopay') || provStr.includes('shopeepay') || provStr.includes('linkaja')
    ) && !provStr.includes('powerbank') && provStr !== 'pb';

    let isPulsaContext = catStr.includes('pulsa') || catStr.includes('cellular') || catStr.includes('seluler') ||
                         ['tsel', 'telkomsel', 'isat', 'indosat', 'tri', 'three', '3', 'xl', 'axis', 'smartfren', 'smart'].includes(provStr);

    let shouldShowCustomCard = isBankOrWallet || isPulsaContext;

    if (shouldShowCustomCard) {
        let providerTitleParam = (window.selectedProviderTitle || selectedProvider || '').replace(/'/g, "\\'");

        let customDataName = "nominal bebas kustom pulsa transfer topup " + provStr;
        if (catStr.includes('bank') || provStr.includes('bank') || catStr.includes('transfer')) {
            customDataName = "nominal bebas kustom transfer bank " + provStr;
        }

        let customCardHTML = `
            <div id="card_custom_amount" 
                 onclick="openCustomAmountModal('${providerTitleParam}')" 
                 class="product-item bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl shadow-md hover:shadow-indigo-200 p-3.5 flex flex-col justify-between transition cursor-pointer active:scale-98 group"
                 data-name="${customDataName}"
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
                        Rp Bebas + Admin/Margin
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

    if ((!products || products.length === 0) && !shouldShowCustomCard) {
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
                     data-brand="${(product.brand || '').toLowerCase()}"
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

function filterBrandList() {
    let input = document.getElementById('search_brand_input');
    let filter = input ? input.value.toLowerCase().trim() : '';
    let buttons = document.querySelectorAll('#brand_list_wrapper .brand-filter-btn');

    buttons.forEach(btn => {
        let brandName = btn.innerText.toLowerCase();
        let brandAttr = btn.getAttribute('data-brand-btn');
        if (brandAttr === 'all' || brandName.includes(filter)) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    });
}

function selectBrandFilter(brandKey) {
    selectedBrand = brandKey.toLowerCase();

    let buttons = document.querySelectorAll('#brand_list_wrapper .brand-filter-btn');
    buttons.forEach(btn => {
        let attr = btn.getAttribute('data-brand-btn');
        if (attr === selectedBrand) {
            btn.className = 'brand-filter-btn shrink-0 text-left px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-bold transition flex items-center justify-between space-x-2';
            if (!btn.querySelector('.fa-check')) {
                btn.insertAdjacentHTML('beforeend', '<i class="fa-solid fa-check text-[10px]"></i>');
            }
        } else {
            btn.className = 'brand-filter-btn shrink-0 text-left px-3.5 py-2 rounded-xl hover:bg-slate-100 text-slate-700 font-bold transition flex items-center justify-between whitespace-nowrap';
            let checkIcon = btn.querySelector('.fa-check');
            if (checkIcon) checkIcon.remove();
        }
    });

    applyProductFilters();
}

function resetBrandSidebarUI() {
    let searchInput = document.getElementById('search_brand_input');
    if (searchInput) searchInput.value = '';
    filterBrandList();
    selectBrandFilter('all');
}

function resetCategoryNavigation() {
    if (navLevel === 3) {
        navLevel = 2;
        selectedProvider = '';
        selectedBrand = 'all';
        window.selectedProviderTitle = '';
        
        document.getElementById('view_products_grid').classList.add('hidden');
        document.getElementById('view_sub_providers').classList.remove('hidden');
        
        let brandSidebar = document.getElementById('brand_sidebar_container');
        if (brandSidebar) brandSidebar.classList.add('hidden');

        togglePriceFilterVisibility(false);
        
        let titleText = document.getElementById('catalog_title_text');
        if (titleText) {
            let labelTitle = 'Operator / Provider';
            if (selectedCategory.includes('handphone') || selectedCategory === 'hp') labelTitle = 'Tipe Handphone';
            else if (selectedCategory.includes('aksesoris')) labelTitle = 'Aksesoris HP';
            else if (selectedCategory.includes('ewallet') || selectedCategory.includes('wallet')) labelTitle = 'E-Wallet';
            else if (selectedCategory.includes('bank') || selectedCategory.includes('transfer')) labelTitle = 'Bank';

            titleText.innerHTML = '<i class="fa-solid fa-list mr-2 text-indigo-600"></i> Pilih ' + labelTitle;
        }

        let grids = [
            'sub_cellular_grid', 
            'sub_ewallet_grid', 
            'sub_bank_grid', 
            'sub_handphone_grid', 
            'sub_aksesoris_grid'
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

    navLevel = 1;
    selectedCategory = '';
    selectedProvider = '';
    selectedBrand = 'all';
    window.selectedProviderTitle = '';
    
    document.getElementById('view_main_categories').classList.remove('hidden');
    document.getElementById('view_sub_providers').classList.add('hidden');
    document.getElementById('view_products_grid').classList.add('hidden');
    
    let brandSidebar = document.getElementById('brand_sidebar_container');
    if (brandSidebar) brandSidebar.classList.add('hidden');

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
        let brand = (item.getAttribute('data-brand') || '').toLowerCase();
        let price = parseFloat(item.getAttribute('data-price')) || 0;

        let matchSearch = searchKeyword === '' || name.includes(searchKeyword) || code.includes(searchKeyword);
        let matchPrice = (price >= minPrice && price <= maxPrice);
        let matchBrand = (selectedBrand === 'all') || brand.includes(selectedBrand) || name.includes(selectedBrand);

        if (matchSearch && matchPrice && matchBrand) {
            posVisibleProducts.push(item);
        } else {
            item.classList.add('hidden');
        }
    });

    let selectEl = document.getElementById('pos_items_per_page');
    if (selectEl) {
        posItemsPerPage = parseInt(selectEl.value) || 12;
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
    posItemsPerPage = parseInt(selectEl ? selectEl.value : 12) || 12;
    posCurrentPage = 1;
    renderPosPagination();
}

function filterProducts(event) {
    applyProductFilters();
}