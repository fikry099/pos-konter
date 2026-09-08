<div id="view_main_categories" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    
    <!-- CARD 1 (PULSA REGULER) -->
    <div onclick="navigateToSubCategory('pulsa', 'Pulsa Reguler')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-phone-volume"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Pulsa Reguler</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">Telkomsel, Indosat, XL, Tri, Axis</p>
        </div>
    </div>

    <!-- CARD 2 (VOUCHER INTERNET) -->
    <div onclick="navigateToSubCategory('voucher', 'Voucher Internet')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-ticket"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Voucher Internet</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">Voucher Fisik & Kuota Data</p>
        </div>
    </div>

    <!-- CARD 3 (KARTU PERDANA) -->
    <div onclick="navigateToSubCategory('perdana', 'Kartu Perdana')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-sim-card"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Kartu Perdana</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">Perdana Segel & Kuota</p>
        </div>
    </div>

    <!-- CARD 4 (HANDPHONE / HP) -->
    <div onclick="navigateToSubCategory('handphone', 'Handphone')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Handphone</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">HP Baru (New) & Second</p>
        </div>
    </div>

    <!-- CARD 5 (TOP-UP E-WALLET) -->
    <div onclick="navigateToSubCategory('ewallet', 'Top-Up E-Wallet')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Top-Up E-Wallet</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">DANA, OVO, GoPay, ShopeePay</p>
        </div>
    </div>

    <!-- CARD 6 (TRANSFER BANK) -->
    <div onclick="navigateToSubCategory('bank', 'Transfer Bank')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Transfer Bank</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">BCA, BRI, Mandiri, BNI, BSI</p>
        </div>
    </div>

    <!-- CARD 7 (TOKEN PLN) -->
    <div onclick="navigateToDirectCategory('token-pln', 'Token PLN')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-3">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Token PLN</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">Strum & Token Listrik</p>
        </div>
    </div>

    <!-- CARD 8 (AKSESORIS HP) -->
    <div onclick="navigateToSubCategory('aksesoris', 'Aksesoris HP')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-1">
            <i class="fa-solid fa-paperclip"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Aksesoris HP</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">Proteksi, Power, Audio, Storage</p>
        </div>
    </div>

    <!-- CARD 9 (SEMUA PRODUK) -->
    <div onclick="showAllProducts()" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0 mb-1">
            <i class="fa-solid fa-border-all"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-sm md:text-base text-slate-800 group-hover:text-indigo-600 transition">Semua Produk</h3>
            <p class="text-xs text-gray-400 mt-1 font-medium">Tampilkan Seluruh Katalog</p>
        </div>
    </div>

</div>