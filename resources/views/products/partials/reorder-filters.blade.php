<div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
    <!-- TAB KATEGORI UTAMA (HANYA PRODUK FISIK) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        <button type="button" onclick="filterMainCategory('all', this)" class="main-cat-btn px-4 py-2 rounded-xl text-xs font-black transition whitespace-nowrap bg-indigo-600 text-white shadow-sm shadow-indigo-200 active:scale-95 cursor-pointer flex items-center gap-1.5">
            <i class="fa-solid fa-border-all"></i>
            <span>Semua Barang Fisik (<span id="count_all_items">{{ $lowStockProducts->count() }}</span>)</span>
        </button>

        @foreach($categories as $cat)
            @php
                $slug = strtolower($cat->slug ?? '');
                // Hanya tampilkan kategori barang fisik
                $isPhysicalCategory = str_contains($slug, 'voucher') || 
                                     str_contains($slug, 'perdana') || 
                                     str_contains($slug, 'aksesoris') || 
                                     str_contains($slug, 'handphone') ||
                                     str_contains($slug, 'fisik');
            @endphp

            @if($isPhysicalCategory)
                @php
                    $icon = 'fa-boxes-stacked';
                    if(str_contains($slug, 'voucher')) $icon = 'fa-ticket';
                    elseif(str_contains($slug, 'perdana')) $icon = 'fa-sim-card';
                    elseif(str_contains($slug, 'aksesoris')) $icon = 'fa-paperclip';
                    elseif(str_contains($slug, 'handphone')) $icon = 'fa-mobile-screen-button';
                @endphp
                <button type="button" 
                        onclick="filterMainCategory('{{ $slug }}', this)" 
                        data-subcategories='@json($cat->allChildren ?? [])'
                        class="main-cat-btn px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap bg-slate-100 hover:bg-slate-200 text-slate-600 active:scale-95 cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid {{ $icon }}"></i>
                    <span>{{ $cat->name }}</span>
                </button>
            @endif
        @endforeach
    </div>

    <!-- FILTER SUB-KATEGORI DINAMIS & SEARCH BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-2 border-t border-slate-100">
        <!-- PILLS SUB-KATEGORI DINAMIS -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider whitespace-nowrap mr-1">SUB KATEGORI:</span>
            <div id="sub_category_pills_container" class="flex items-center gap-1.5">
                <button type="button" onclick="filterSubCategory('all', this)" class="sub-cat-pill px-3 py-1.5 rounded-lg text-xs font-black transition whitespace-nowrap bg-indigo-100 text-indigo-700 active:scale-95 cursor-pointer">
                    Semua
                </button>
            </div>
        </div>

        <!-- SEARCH INPUT -->
        <div class="relative min-w-[240px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="reorder_search_input" onkeyup="applyReorderFilters()" placeholder="Cari nama produk / kode SKU..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white font-medium transition">
        </div>
    </div>
</div>