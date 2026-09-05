<!-- BAR PENCARIAN & SUB-FILTER DINAMIS -->
<div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        
        <!-- SUB-FILTER PILLS DINAMIS -->
        <div class="flex items-center space-x-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 no-scrollbar min-w-0">
            <span class="text-[11px] sm:text-xs text-gray-400 font-bold uppercase tracking-wider shrink-0 mr-1" id="filter_label">Sub Kategori:</span>
            <div id="provider_pills_container" class="flex items-center space-x-2 shrink-0">
                <!-- Tombol Pill digenerate otomatis via JS -->
            </div>
        </div>

        <!-- SEARCH INPUT -->
        <div class="relative w-full sm:w-72 md:w-80 shrink-0">
            <input type="text" id="search_product_input" onkeyup="filterProductsTable()" placeholder="Cari nama produk / kode SKU..." class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white font-medium transition">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400 text-xs sm:text-sm"></i>
        </div>

    </div>
</div>