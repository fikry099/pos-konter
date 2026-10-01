<!-- HEADER TOPBAR KATALOG & FILTER (RESPONSIF TABLET & MOBILE) -->
<div class="bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200/80 shrink-0">
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2.5 sm:gap-3">
        
        <!-- BAGIAN KIRI: TOMBOL KEMBALI & JUDUL KATALOG -->
        <div class="flex items-center space-x-2.5 min-w-0">
            <button type="button" id="btn_back_category" onclick="resetCategoryNavigation()" class="hidden bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center shrink-0 border border-slate-200 cursor-pointer" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h1 id="catalog_title_text" class="text-xs sm:text-base md:text-lg font-extrabold text-slate-800 flex items-center truncate">
                <i class="fa-solid fa-boxes-stacked text-indigo-600 mr-2 text-sm sm:text-lg shrink-0"></i> 
                <span class="truncate">Katalog Utama</span>
            </h1>
        </div>

        <!-- BAGIAN KANAN: RENTANG HARGA & PENCARIAN (RESPONSIF AUTO-WRAP) -->
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-end gap-2 w-full md:w-auto">
            
            <!-- CONTAINER RENTANG HARGA (MIN & MAX) -->
            <div id="price_filter_container" class="hidden items-center gap-1.5 shrink-0">
                <div class="relative w-20 sm:w-24">
                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-slate-400 text-[10px] font-bold">Rp</span>
                    <input type="text" id="filter_min_price" oninput="formatPriceInput(this)" placeholder="Min" class="w-full pl-6 pr-2 py-1.5 sm:py-2 text-xs bg-slate-50 text-slate-800 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-semibold">
                </div>
                <span class="text-slate-400 text-xs font-bold">-</span>
                <div class="relative w-20 sm:w-24">
                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-slate-400 text-[10px] font-bold">Rp</span>
                    <input type="text" id="filter_max_price" oninput="formatPriceInput(this)" placeholder="Max" class="w-full pl-6 pr-2 py-1.5 sm:py-2 text-xs bg-slate-50 text-slate-800 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-semibold">
                </div>
            </div>

            <!-- INPUT PENCARIAN REALTIME -->
            <div class="relative flex-1 sm:flex-none sm:w-48 md:w-56">
                <input type="text" id="search_product" onkeyup="filterProducts(event)" placeholder="Cari nama / kode..." class="w-full pl-8 pr-3 py-1.5 sm:py-2 text-xs sm:text-sm bg-slate-50 text-slate-800 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-medium">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
            
        </div>
    </div>
</div>