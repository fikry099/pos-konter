@php
    // Gunakan $stocks atau fallback ke $products jika dikirim dari controller lama
    $listItems = $stocks ?? $products ?? collect();
    $totalCount = is_countable($listItems) ? count($listItems) : 0;
@endphp

<!-- TAB KATEGORI UTAMA (HANYA PRODUK FISIK) -->
<div class="bg-white p-2.5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-2 overflow-x-auto no-scrollbar">
    <button type="button" onclick="switchCategoryTab('all', this)" class="category-tab active-tab bg-indigo-600 text-white px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl text-xs sm:text-sm font-extrabold transition flex items-center space-x-2 shrink-0 shadow-md shadow-indigo-200 whitespace-nowrap cursor-pointer">
        <i class="fa-solid fa-border-all text-xs sm:text-sm"></i>
        <span>Semua Barang Fisik ({{ $totalCount }})</span>
    </button>

    <button type="button" onclick="switchCategoryTab('voucher', this)" class="category-tab bg-slate-50 text-gray-700 hover:bg-slate-100 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl text-xs sm:text-sm font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200/60 whitespace-nowrap cursor-pointer">
        <i class="fa-solid fa-ticket text-blue-500 text-xs sm:text-sm"></i>
        <span>Voucher Internet</span>
    </button>

    <button type="button" onclick="switchCategoryTab('perdana', this)" class="category-tab bg-slate-50 text-gray-700 hover:bg-slate-100 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl text-xs sm:text-sm font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200/60 whitespace-nowrap cursor-pointer">
        <i class="fa-solid fa-sim-card text-rose-500 text-xs sm:text-sm"></i>
        <span>Kartu Perdana</span>
    </button>

    <button type="button" onclick="switchCategoryTab('aksesoris', this)" class="category-tab bg-slate-50 text-gray-700 hover:bg-slate-100 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl text-xs sm:text-sm font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200/60 whitespace-nowrap cursor-pointer">
        <i class="fa-solid fa-plug text-purple-500 text-xs sm:text-sm"></i>
        <span>Aksesoris HP</span>
    </button>
</div>