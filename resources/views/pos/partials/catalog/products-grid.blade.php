<div id="view_products_grid" class="hidden space-y-4">
    <!-- LAYOUT 2 KOLOM: SIDEBAR FILTER MEREK + GRID PRODUK -->
    <div class="flex flex-col lg:flex-row gap-4 items-start">
        
        <!-- SIDEBAR FILTER MEREK (HANYA MUNCUL JIKA KATEGORI MEMILIKI SUB-MEREK SEPERTI AKSESORIS) -->
        <div id="brand_sidebar_container" class="w-full lg:w-64 bg-white rounded-3xl p-4 border border-slate-200/90 shadow-sm shrink-0 hidden">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-indigo-600"></i>
                    <span>Pilih Merek</span>
                </h3>
                <span id="brand_count_badge" class="text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full">23</span>
            </div>

            <!-- Mini Search Merek -->
            <div class="relative mb-3">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="search_brand_input" onkeyup="filterBrandList()" placeholder="Cari merek..." 
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <!-- List Merek Scrollable -->
            <div id="brand_list_wrapper" class="space-y-1 max-h-[420px] overflow-y-auto pr-1 text-xs font-bold text-slate-600 custom-scrollbar">
                <button type="button" onclick="selectBrandFilter('all')" data-brand-btn="all" 
                        class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl bg-indigo-600 text-white transition flex items-center justify-between">
                    <span>Semua Merek</span>
                    <i class="fa-solid fa-check text-[10px]"></i>
                </button>
                
                <!-- Daftar Merek Lengkap -->
                <button type="button" onclick="selectBrandFilter('robot')" data-brand-btn="robot" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Robot</span></button>
                <button type="button" onclick="selectBrandFilter('olike')" data-brand-btn="olike" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Olike</span></button>
                <button type="button" onclick="selectBrandFilter('oraimo')" data-brand-btn="oraimo" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Oraimo</span></button>
                <button type="button" onclick="selectBrandFilter('log-on')" data-brand-btn="log-on" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Log-on</span></button>
                <button type="button" onclick="selectBrandFilter('vivan')" data-brand-btn="vivan" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Vivan</span></button>
                <button type="button" onclick="selectBrandFilter('wellcomm')" data-brand-btn="wellcomm" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Wellcomm</span></button>
                <button type="button" onclick="selectBrandFilter('roket')" data-brand-btn="roket" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Rocket</span></button>
                <button type="button" onclick="selectBrandFilter('luna')" data-brand-btn="luna" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Luna</span></button>
                <button type="button" onclick="selectBrandFilter('ugreen')" data-brand-btn="ugreen" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Ugreen</span></button>
                <button type="button" onclick="selectBrandFilter('vgen')" data-brand-btn="vgen" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>V-Gen</span></button>
                <button type="button" onclick="selectBrandFilter('camp')" data-brand-btn="camp" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Camp</span></button>
                <button type="button" onclick="selectBrandFilter('rexi')" data-brand-btn="rexi" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Rexi</span></button>
                <button type="button" onclick="selectBrandFilter('minimo')" data-brand-btn="minimo" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Minimo</span></button>
                <button type="button" onclick="selectBrandFilter('fantech')" data-brand-btn="fantech" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Fantech</span></button>
                <button type="button" onclick="selectBrandFilter('dap')" data-brand-btn="dap" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>DAP</span></button>
                <button type="button" onclick="selectBrandFilter('mi')" data-brand-btn="mi" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Mi / Xiaomi</span></button>
                <button type="button" onclick="selectBrandFilter('sam')" data-brand-btn="sam" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>SAM / Samsung</span></button>
                <button type="button" onclick="selectBrandFilter('fleco')" data-brand-btn="fleco" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Fleco</span></button>
                <button type="button" onclick="selectBrandFilter('rapa')" data-brand-btn="rapa" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Rapa</span></button>
                <button type="button" onclick="selectBrandFilter('hikaru')" data-brand-btn="hikaru" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Hikaru</span></button>
                <button type="button" onclick="selectBrandFilter('kin')" data-brand-btn="kin" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Kin</span></button>
                <button type="button" onclick="selectBrandFilter('foomee')" data-brand-btn="foomee" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Foomee</span></button>
                <button type="button" onclick="selectBrandFilter('memax')" data-brand-btn="memax" class="brand-filter-btn w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition flex items-center justify-between"><span>Memax</span></button>
            </div>
        </div>

        <!-- GRID UTAMA PRODUK -->
        <div class="flex-1 w-full">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-3.5">
                <!-- KARTU NOMINAL BEBAS -->
                <div id="card_custom_amount" class="hidden bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl shadow-md hover:shadow-indigo-200 p-3.5 flex flex-col justify-between transition cursor-pointer active:scale-98 group"
                     onclick="openCustomAmountModal()">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full bg-white/20 text-white backdrop-blur-sm">Kustom</span>
                            <i class="fa-solid fa-pen-to-square text-xs text-indigo-200"></i>
                        </div>
                        <h4 class="font-extrabold text-white text-xs md:text-sm line-clamp-2 mb-1 leading-snug">Nominal Bebas / Lainnya</h4>
                        <p class="text-[11px] text-indigo-100 mb-2">Ketik nominal kustom pembeli</p>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-indigo-200 mb-2">Rp Bebas + Admin</div>
                        <button type="button" class="w-full bg-white text-indigo-700 hover:bg-indigo-50 text-xs font-black py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm active:scale-95">
                            <i class="fa-solid fa-keyboard text-xs"></i>
                            <span>Input Nominal</span>
                        </button>
                    </div>
                </div>

                <!-- LOOPING PRODUK DARI DATABASE -->
                @forelse($products as $product)
                    @php
                        $currentCat = $product->category;
                        $parentCat = $currentCat ? $currentCat->parent : null;
                        $grandParentCat = $parentCat ? $parentCat->parent : null;
                        $catHierarchy = '';
                        if ($grandParentCat) { $catHierarchy .= strtolower($grandParentCat->name) . ' '; }
                        if ($parentCat) { $catHierarchy .= strtolower($parentCat->name) . ' '; }
                        if ($currentCat) { $catHierarchy .= strtolower($currentCat->name) . ' ' . strtolower($currentCat->slug); }
                        $prodName = strtolower($product->name);
                        $prodCode = strtolower($product->code ?? '');
                        $prodBrand = strtolower($product->brand ?? ''); // Atribut Merek
                        $productType   = strtolower(trim($product->type ?? 'physical'));
                        $isDigital     = ($productType === 'digital');
                        $actualStock   = isset($product->current_stock) ? (int)$product->current_stock : (int)$product->stock;
                        $isOutOfStock  = !$isDigital && ($actualStock <= 0);
                    @endphp
                    <div class="product-item cat-{{ $product->category_id }} bg-white rounded-2xl shadow-sm border border-gray-200/90 p-3.5 flex flex-col justify-between transition group {{ $isOutOfStock ? 'opacity-60 bg-gray-50 border-rose-200 cursor-not-allowed' : 'hover:border-indigo-500 cursor-pointer active:scale-98' }}" 
                         @if(!$isOutOfStock) onclick="openAddToCartModal({{ json_encode($product) }})" @endif
                         data-category="{{ trim($catHierarchy) }}"
                         data-name="{{ $prodName }}" 
                         data-code="{{ $prodCode }}"
                         data-brand="{{ $prodBrand }}"
                         data-price="{{ $product->selling_price }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full {{ $isDigital ? 'bg-blue-100 text-blue-700' : ($isOutOfStock ? 'bg-rose-100 text-rose-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $isDigital ? 'Digital' : 'Fisik' }}
                                </span>
                                @if(!$isDigital)
                                    <span class="text-[11px] font-semibold {{ $isOutOfStock ? 'text-rose-600 font-black' : ($actualStock <= $product->min_stock ? 'text-amber-600 font-bold animate-pulse' : 'text-gray-500') }}">
                                        {{ $isOutOfStock ? 'Stok Habis' : 'Stok: ' . $actualStock }}
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-800 text-xs md:text-sm line-clamp-2 mb-1 {{ !$isOutOfStock ? 'group-hover:text-indigo-600' : 'text-gray-400' }} transition leading-snug">
                                {{ $product->name }}
                            </h4>
                            <p class="text-[11px] text-gray-400 mb-2 font-mono">{{ $product->code ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm md:text-base font-black {{ !$isOutOfStock ? 'text-indigo-700' : 'text-gray-400' }} mb-2">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </div>
                            @if($isOutOfStock)
                                <button type="button" disabled class="w-full bg-gray-200 text-gray-400 text-xs font-bold py-2.5 rounded-xl flex items-center justify-center space-x-1.5 cursor-not-allowed">
                                    <i class="fa-solid fa-ban text-xs"></i>
                                    <span>Stok Habis</span>
                                </button>
                            @else
                                <button type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm active:scale-95">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Tambah</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-10 text-center text-gray-400 border border-gray-200">
                        <i class="fa-solid fa-box-open text-4xl mb-2"></i>
                        <p class="text-sm font-semibold">Pilih kategori atau provider untuk menampilkan produk.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- NAVBAR PAGINASI GRID POS DINAMIS -->
    <div id="pos_pagination_container" class="bg-white p-3.5 rounded-2xl border border-gray-200/90 shadow-sm flex flex-col sm:flex-row items-center justify-start gap-3 sm:gap-4 text-xs font-bold text-slate-600 pr-16 sm:pr-20">
        <div class="flex items-center space-x-2">
            <span>Tampilkan</span>
            <select id="pos_items_per_page" onchange="changePosPerPage()" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 font-bold text-slate-800 focus:outline-none focus:border-indigo-500 cursor-pointer">
                <option value="12" selected>12</option>
                <option value="24">24</option>
                <option value="48">48</option>
            </select>
            <span>produk</span>
        </div>
        <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
        <div id="pos_pagination_buttons" class="flex items-center space-x-1.5"></div>
        <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
        <div id="pos_pagination_info" class="text-slate-500 font-bold text-[11px] sm:text-xs">
            Menampilkan 0 dari 0 produk
        </div>
    </div>
</div>