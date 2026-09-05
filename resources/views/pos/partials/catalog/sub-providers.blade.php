<div id="view_sub_providers" class="hidden">
    
    <!-- CARDS BRAND PULSA, VOUCHER & KARTU PERDANA (DIPAKAI BERSAMA) -->
    <div id="sub_cellular_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div onclick="selectProviderFilter('telkomsel', 'Telkomsel')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-red-600 group-hover:scale-105 transition">Telkomsel</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Simpati / AS / By.U</span>
        </div>
        <div onclick="selectProviderFilter('indosat', 'Indosat')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-amber-500 group-hover:scale-105 transition">Indosat</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">IM3 / Freedom</span>
        </div>
        <div onclick="selectProviderFilter('xl', 'XL Axiata')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-blue-600 group-hover:scale-105 transition">XL Axiata</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">XL / Extra Combo</span>
        </div>
        <div onclick="selectProviderFilter('three', 'Tri (3)')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-purple-600 group-hover:scale-105 transition">Tri (3)</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Happy / AON</span>
        </div>
        <div onclick="selectProviderFilter('axis', 'Axis')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-fuchsia-600 group-hover:scale-105 transition">Axis</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Bronet / Owsem</span>
        </div>
        <div onclick="selectProviderFilter('smartfren', 'Smartfren')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-rose-500 group-hover:scale-105 transition">Smartfren</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Unlimited / Nonstop</span>
        </div>
    </div>

    <!-- CARDS BRAND E-WALLET -->
    <div id="sub_ewallet_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div onclick="selectProviderFilter('dana', 'DANA')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-sky-500 group-hover:scale-105 transition">DANA</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Saldo DANA</span>
        </div>
        <div onclick="selectProviderFilter('gopay', 'GoPay')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-emerald-600 group-hover:scale-105 transition">GoPay</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Customer / Driver</span>
        </div>
        <div onclick="selectProviderFilter('ovo', 'OVO')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-purple-700 group-hover:scale-105 transition">OVO</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Saldo OVO</span>
        </div>
        <div onclick="selectProviderFilter('shopee', 'ShopeePay')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-orange-500 group-hover:scale-105 transition">ShopeePay</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">ShopeePay</span>
        </div>
        <div onclick="selectProviderFilter('linkaja', 'LinkAja')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-red-600 group-hover:scale-105 transition">LinkAja</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">LinkAja</span>
        </div>
        <div onclick="selectProviderFilter('maxim', 'Maxim')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-yellow-500 group-hover:scale-105 transition">Maxim</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Driver / Passenger</span>
        </div>
    </div>

    <!-- CARDS BANK TRANSFER -->
    <div id="sub_bank_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div onclick="selectProviderFilter('bca', 'Bank BCA')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-blue-700 group-hover:scale-105 transition">BCA</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Bank Central Asia</span>
        </div>
        <div onclick="selectProviderFilter('bri', 'Bank BRI')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-blue-800 group-hover:scale-105 transition">BRI</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Bank Rakyat Indonesia</span>
        </div>
        <div onclick="selectProviderFilter('mandiri', 'Bank Mandiri')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-amber-600 group-hover:scale-105 transition">Mandiri</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Bank Mandiri</span>
        </div>
        <div onclick="selectProviderFilter('bni', 'Bank BNI')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-teal-600 group-hover:scale-105 transition">BNI</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Bank Negara Indonesia</span>
        </div>
        <div onclick="selectProviderFilter('bsi', 'Bank BSI')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-emerald-600 group-hover:scale-105 transition">BSI</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">Bank Syariah Indonesia</span>
        </div>
        <div onclick="selectProviderFilter('permata', 'Permata / CIMB')" class="bg-white p-6 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md flex flex-col items-center justify-center text-center cursor-pointer transition active:scale-95 group">
            <span class="font-black text-xl text-purple-600 group-hover:scale-105 transition">Lainnya</span>
            <span class="text-xs text-gray-400 mt-2 font-medium">CIMB, Permata, Danamon</span>
        </div>
    </div>

    <!-- CARDS SUB-AKSESORIS HP -->
    <div id="sub_aksesoris_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">

        <!-- 1. PROTEKSI -->
        <div onclick="selectProviderFilter('proteksi', 'Proteksi')" class="bg-white hover:bg-rose-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-rose-600 transition">Proteksi</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Casing, TG, Hydrogel</span>
            </div>
        </div>

        <!-- 2. POWER -->
        <div onclick="selectProviderFilter('power', 'Power')" class="bg-white hover:bg-amber-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-amber-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-100 text-amber-500 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-amber-600 transition">Power</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Charger, Kabel, PB</span>
            </div>
        </div>

        <!-- 3. AUDIO -->
        <div onclick="selectProviderFilter('audio', 'Audio')" class="bg-white hover:bg-indigo-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-indigo-600 transition">Audio</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">TWS, Headset, Speaker</span>
            </div>
        </div>

        <!-- 4. PENYIMPANAN -->
        <div onclick="selectProviderFilter('penyimpanan', 'Penyimpanan')" class="bg-white hover:bg-emerald-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-emerald-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-sd-card"></i>
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-emerald-600 transition">Penyimpanan</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Flashdisk, MicroSD</span>
            </div>
        </div>

        <!-- 5. MOUNT & STAND -->
        <div onclick="selectProviderFilter('mount-stand', 'Mount & Stand')" class="bg-white hover:bg-sky-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-sky-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-mobile-retro"></i>
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-sky-600 transition">Mount & Stand</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Holder, Tripod</span>
            </div>
        </div>

        <!-- 6. SEMUA AKSESORIS -->
        <div onclick="selectProviderFilter('aksesoris', 'Semua Aksesoris')" class="bg-white hover:bg-slate-100/60 p-5 rounded-3xl border border-slate-200/90 hover:border-slate-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-border-all"></i>
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-slate-700 transition">Semua</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Seluruh Aksesoris</span>
            </div>
        </div>

    </div>
</div>