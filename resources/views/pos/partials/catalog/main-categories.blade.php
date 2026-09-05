<div id="view_main_categories" class="grid grid-cols-2 gap-3.5">
    
    <!-- CARD 1 (PULSA REGULER) -->
    <div onclick="navigateToSubCategory('pulsa', 'Pulsa Reguler')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Pulsa Reguler</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Telkomsel, Indosat, XL, Tri, Axis</p>
        </div>
    </div>

    <!-- CARD 2 (VOUCHER INTERNET) -->
    <div onclick="navigateToSubCategory('voucher', 'Voucher Internet')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-ticket"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Voucher Internet</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Voucher Fisik & Kuota Data</p>
        </div>
    </div>

    <!-- CARD 3 (KARTU PERDANA) -->
    <div onclick="navigateToSubCategory('perdana', 'Kartu Perdana')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-sim-card"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Kartu Perdana</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Perdana Segel & Kuota</p>
        </div>
    </div>

    <!-- CARD 4 (TOP-UP E-WALLET) -->
    <div onclick="navigateToSubCategory('ewallet', 'Top-Up E-Wallet')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Top-Up E-Wallet</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">DANA, OVO, GoPay, ShopeePay</p>
        </div>
    </div>

    <!-- CARD 5 (TRANSFER BANK) -->
    <div onclick="navigateToSubCategory('bank', 'Transfer Bank')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Transfer Bank</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">BCA, BRI, Mandiri, BNI, BSI</p>
        </div>
    </div>

    <!-- CARD 6 (TOKEN PLN) -->
    <div onclick="navigateToDirectCategory('token-pln', 'Token PLN')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Token PLN</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Strum & Token Listrik</p>
        </div>
    </div>

    <!-- CARD 7 (AKSESORIS HP) -->
    <div onclick="navigateToSubCategory('aksesoris', 'Aksesoris HP')" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-headphones"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Aksesoris HP</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Proteksi, Power, Audio, Storage</p>
        </div>
    </div>

    <!-- CARD 8 (SEMUA PRODUK - TAMPILAN DISERAGAMKAN VERTIKAL) -->
    <div onclick="showAllProducts()" class="bg-white hover:bg-indigo-50/60 p-4 rounded-2xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-98">
        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner shrink-0">
            <i class="fa-solid fa-border-all"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm group-hover:text-indigo-600">Semua Produk</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Tampilkan Seluruh Katalog</p>
        </div>
    </div>

</div>