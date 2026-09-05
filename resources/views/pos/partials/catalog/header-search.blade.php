<div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-200/80 shrink-0">
    <div class="flex flex-row items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
        
        <!-- BAGIAN KIRI: TOMBOL KEMBALI & JUDUL KATALOG -->
        <div class="flex items-center space-x-3 shrink-0">
            <button type="button" id="btn_back_category" onclick="resetCategoryNavigation()" class="hidden bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 w-10 h-10 rounded-xl text-sm font-bold transition items-center justify-center shrink-0 border border-slate-200 cursor-pointer" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h1 id="catalog_title_text" class="text-sm sm:text-lg font-extrabold text-slate-800 flex items-center whitespace-nowrap">
                <i class="fa-solid fa-boxes-stacked text-indigo-600 mr-2 text-base sm:text-lg"></i> Katalog Utama
            </h1>
        </div>

        <!-- BAGIAN KANAN: RENTANG HARGA & PENCARIAN -->
        <div class="flex flex-row items-center gap-2 ml-auto shrink-0">
            
            <!-- CONTAINER RENTANG HARGA -->
            <div id="price_filter_container" class="hidden items-center gap-1.5 sm:gap-2">
                <div class="relative w-24 sm:w-28">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 text-[10px] sm:text-xs font-bold">Rp</span>
                    <input type="text" id="filter_min_price" oninput="formatPriceInput(this)" placeholder="Min" class="w-full pl-7 pr-2 py-2 text-xs bg-slate-50 text-slate-800 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-medium">
                </div>
                <span class="text-slate-400 text-xs">-</span>
                <div class="relative w-24 sm:w-28">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 text-[10px] sm:text-xs font-bold">Rp</span>
                    <input type="text" id="filter_max_price" oninput="formatPriceInput(this)" placeholder="Max" class="w-full pl-7 pr-2 py-2 text-xs bg-slate-50 text-slate-800 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-medium">
                </div>
            </div>

            <!-- FORM PENCARIAN SERVER-SIDE + CLIENT-SIDE -->
            <form action="{{ route('pos.index') }}" method="GET" class="relative w-40 sm:w-60">
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                <input type="text" name="search" id="search_product" value="{{ request('search') }}" onkeyup="filterProducts()" placeholder="Cari & Enter..." class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-slate-50 text-slate-800 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-medium">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs sm:text-sm"></i>
            </form>
            
        </div>
    </div>
</div>