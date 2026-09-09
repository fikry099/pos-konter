<div id="view_products_grid" class="hidden space-y-4">
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-3.5">
        
        <!-- KARTU NOMINAL BEBAS -->
        <div id="card_custom_amount" class="hidden bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl shadow-md hover:shadow-indigo-200 p-3.5 flex flex-col justify-between transition cursor-pointer active:scale-98 group"
             onclick="openCustomAmountModal()">
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
                <p class="text-[11px] text-indigo-100 mb-2">Ketik nominal kustom pembeli</p>
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

        <!-- LOOPING PRODUK AWAL DARI DATABASE (APABILA ADA DARI CONTROLLER) -->
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

                $productType   = strtolower(trim($product->type ?? 'physical'));
                $isDigital     = ($productType === 'digital');
                $actualStock   = isset($product->current_stock) ? (int)$product->current_stock : (int)$product->stock;
                $isOutOfStock  = !$isDigital && ($actualStock <= 0);
            @endphp

            <div class="product-item cat-{{ $product->category_id }} bg-white rounded-2xl shadow-sm border border-gray-200/90 p-3.5 flex flex-col justify-between transition group {{ $isOutOfStock ? 'opacity-60 bg-gray-50 border-rose-200 cursor-not-allowed' : 'hover:border-indigo-500 cursor-pointer active:scale-98' }}" 
                 @if(!$isOutOfStock)
                     onclick="openAddToCartModal({{ json_encode($product) }})"
                 @endif
                 data-category="{{ trim($catHierarchy) }}"
                 data-name="{{ $prodName }}" 
                 data-code="{{ $prodCode }}"
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

    <!-- NAVBAR PAGINASI GRID POS DINAMIS -->
<div id="pos_pagination_container" class="bg-white p-3 rounded-2xl border border-gray-200/90 shadow-sm flex flex-col sm:flex-row items-center justify-start gap-4 text-xs font-semibold text-gray-600">
    <!-- Pilihan Per Halaman -->
    <div class="flex items-center space-x-2">
        <span>Tampilkan</span>
        <select id="pos_items_per_page" onchange="changePosPerPage()" class="bg-gray-50 border border-gray-200 rounded-xl px-2 py-1 font-bold text-gray-800 focus:outline-none cursor-pointer">
            <option value="12" selected>12</option>
            <option value="24">24</option>
            <option value="48">48</option>
        </select>
        <span>produk</span>
    </div>

    <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>

    <!-- Tombol Angka Paginasi -->
    <div id="pos_pagination_buttons" class="flex items-center space-x-1">
        <!-- Tombol halaman di-render via JS -->
    </div>

    <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>

    <!-- Info Teks -->
    <div id="pos_pagination_info" class="text-gray-500 font-medium text-[11px] sm:text-xs">
        Menampilkan 0 dari 0 produk
    </div>
</div>
</div>