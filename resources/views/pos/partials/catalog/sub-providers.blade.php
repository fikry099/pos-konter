<div id="view_sub_providers" class="hidden">
    
    <!-- LEVEL 2: CARDS BRAND PULSA, VOUCHER & KARTU PERDANA (DENGAN GAMBAR ICON) -->
    <div id="sub_cellular_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        
        <!-- Telkomsel -->
        <div onclick="selectProviderFilter('telkomsel', 'Telkomsel')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-red-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-red-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/provider/telkomsel.png') }}" alt="Telkomsel" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-red-600 group-hover:scale-105 transition block">Telkomsel</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Simpati / AS / By.U</span>
            </div>
        </div>

        <!-- Indosat -->
        <div onclick="selectProviderFilter('indosat', 'Indosat')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-amber-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/provider/indosat-ooredoo.png') }}" alt="Indosat" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-amber-500 group-hover:scale-105 transition block">Indosat</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">IM3 / Freedom</span>
            </div>
        </div>

        <!-- XL Axiata -->
        <div onclick="selectProviderFilter('xl', 'XL Axiata')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-blue-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/provider/xl.png') }}" alt="XL Axiata" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-blue-600 group-hover:scale-105 transition block">XL Axiata</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">XL / Extra Combo</span>
            </div>
        </div>

        <!-- Tri (3) -->
        <div onclick="selectProviderFilter('three', 'Tri (3)')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-purple-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-purple-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/provider/tri.png') }}" alt="Tri (3)" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-purple-600 group-hover:scale-105 transition block">Tri (3)</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Happy / AON</span>
            </div>
        </div>

        <!-- Axis -->
        <div onclick="selectProviderFilter('axis', 'Axis')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-fuchsia-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-fuchsia-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/provider/axis.png') }}" alt="Axis" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-fuchsia-600 group-hover:scale-105 transition block">Axis</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Bronet / Owsem</span>
            </div>
        </div>

        <!-- Smartfren -->
        <div onclick="selectProviderFilter('smartfren', 'Smartfren')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-rose-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/provider/smartfren.png') }}" alt="Smartfren" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-rose-500 group-hover:scale-105 transition block">Smartfren</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Unlimited / Nonstop</span>
            </div>
        </div>

    </div>

    <!-- LEVEL 2: CARDS BRAND E-WALLET (DENGAN GAMBAR ICON LOGO) -->
    <div id="sub_ewallet_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        
        <!-- DANA -->
        <div onclick="openCustomAmountModal('DANA')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-sky-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-sky-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ewallet/dana.png') }}" alt="DANA" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-sky-500 group-hover:scale-105 transition block">DANA</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Saldo DANA</span>
            </div>
        </div>

        <!-- GoPay -->
        <div onclick="openCustomAmountModal('GoPay')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-emerald-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ewallet/gopay.png') }}" alt="GoPay" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-emerald-600 group-hover:scale-105 transition block">GoPay</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Customer / Driver</span>
            </div>
        </div>

        <!-- OVO -->
        <div onclick="openCustomAmountModal('OVO')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-purple-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-purple-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ewallet/ovo.png') }}" alt="OVO" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-purple-700 group-hover:scale-105 transition block">OVO</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Saldo OVO</span>
            </div>
        </div>

        <!-- ShopeePay -->
        <div onclick="openCustomAmountModal('ShopeePay')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-orange-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-orange-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ewallet/shopeepay.png') }}" alt="ShopeePay" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-orange-500 group-hover:scale-105 transition block">ShopeePay</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">ShopeePay</span>
            </div>
        </div>

        <!-- LinkAja -->
        <div onclick="openCustomAmountModal('LinkAja')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-red-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-red-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ewallet/linkaja.png') }}" alt="LinkAja" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-red-600 group-hover:scale-105 transition block">LinkAja</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">LinkAja</span>
            </div>
        </div>

        <!-- Maxim -->
        <div onclick="openCustomAmountModal('Maxim')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-yellow-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-yellow-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ewallet/maxim.png') }}" alt="Maxim" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-yellow-500 group-hover:scale-105 transition block">Maxim</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block">Driver / Passenger</span>
            </div>
        </div>

    </div>

    <!-- LEVEL 2: CARDS BANK TRANSFER (DENGAN GAMBAR ICON LOGO) -->
    <div id="sub_bank_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        
        <!-- BCA -->
        <div onclick="openCustomAmountModal('Bank BCA')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-blue-500 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/bca.png') }}" alt="BCA" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-blue-700 group-hover:scale-105 transition block">BCA</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Central Asia</span>
            </div>
        </div>

        <!-- BRI -->
        <div onclick="openCustomAmountModal('Bank BRI')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-blue-600 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/bri.png') }}" alt="BRI" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-blue-900 group-hover:scale-105 transition block">BRI</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Rakyat Indonesia</span>
            </div>
        </div>

        <!-- MANDIRI -->
        <div onclick="openCustomAmountModal('Bank Mandiri')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-amber-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/mandiri.png') }}" alt="Mandiri" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-amber-500 group-hover:scale-105 transition block">Mandiri</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Mandiri</span>
            </div>
        </div>

        <!-- BNI -->
        <div onclick="openCustomAmountModal('Bank BNI')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-teal-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-teal-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/bni.png') }}" alt="BNI" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-teal-600 group-hover:scale-105 transition block">BNI</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Negara Indonesia</span>
            </div>
        </div>

        <!-- BSI -->
        <div onclick="openCustomAmountModal('Bank BSI')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-emerald-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/bsi.png') }}" alt="BSI" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-emerald-600 group-hover:scale-105 transition block">BSI</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Syariah Indonesia</span>
            </div>
        </div>

        <!-- SEABANK -->
        <div onclick="openCustomAmountModal('SeaBank')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-orange-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-orange-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/seabank.png') }}" alt="SeaBank" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-orange-500 group-hover:scale-105 transition block">SeaBank</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank SeaBank Indonesia</span>
            </div>
        </div>

        <!-- BANK JAGO -->
        <div onclick="openCustomAmountModal('Bank Jago')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-amber-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/bank-jago.png') }}" alt="Bank Jago" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-amber-600 group-hover:scale-105 transition block">Bank Jago</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Jago Tbk</span>
            </div>
        </div>

        <!-- CIMB NIAGA -->
        <div onclick="openCustomAmountModal('CIMB Niaga')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-rose-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/cimb-niaga.png') }}" alt="CIMB Niaga" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-rose-700 group-hover:scale-105 transition block">CIMB Niaga</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank CIMB Niaga</span>
            </div>
        </div>

        <!-- PERMATA -->
        <div onclick="openCustomAmountModal('Bank Permata')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-emerald-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/permata.png') }}" alt="Bank Permata" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-emerald-700 group-hover:scale-105 transition block">Permata</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Permata</span>
            </div>
        </div>

        <!-- DANAMON -->
        <div onclick="openCustomAmountModal('Bank Danamon')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-yellow-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-yellow-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/danamon.png') }}" alt="Bank Danamon" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-yellow-600 group-hover:scale-105 transition block">Danamon</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Danamon</span>
            </div>
        </div>

        <!-- BTN -->
        <div onclick="openCustomAmountModal('Bank BTN')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-blue-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/btn.png') }}" alt="Bank BTN" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-blue-800 group-hover:scale-105 transition block">BTN</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Bank Tabungan Negara</span>
            </div>
        </div>

        <!-- BANK BPD / DAERAH -->
        <div onclick="openCustomAmountModal('Bank Daerah (BPD)')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-sky-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-sky-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/bank/bank-bpd.png') }}" alt="Bank BPD" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-sky-700 group-hover:scale-105 transition block">Bank BPD</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">BJB, Jatim, Jateng, dll</span>
            </div>
        </div>

        <!-- BANK LAINNYA -->
        <div onclick="openCustomAmountModal('Bank Lainnya')" class="bg-white p-5 rounded-3xl border border-slate-200/90 hover:border-purple-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 text-2xl md:text-3xl group-hover:scale-110 transition shadow-inner shrink-0">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <span class="font-black text-base md:text-lg text-purple-600 group-hover:scale-105 transition block">Lainnya</span>
                <span class="text-xs text-gray-400 mt-0.5 font-medium block truncate w-full">Transfer Bank Lain</span>
            </div>
        </div>

    </div>

    <!-- LEVEL 2: CARDS SUB-AKSESORIS HP (9 KATEGORI LANGSUNG DENGAN GAMBAR ICON) -->
    <div id="sub_aksesoris_grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        
        <!-- 1. Cable Data & AUX -->
        <div onclick="selectProviderFilter('cable-data-aux', 'Cable Data & AUX')" class="bg-white hover:bg-indigo-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-indigo-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/cabel-data.png') }}" alt="Cable Data & AUX" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-indigo-600 transition">Cable Data & AUX</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Type-C, Micro, Lightning, AUX</span>
            </div>
        </div>

        <!-- 2. Adaptor / Kepala -->
        <div onclick="selectProviderFilter('adaptor-kepala', 'Adaptor / Kepala')" class="bg-white hover:bg-amber-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-amber-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/kepala-charger.png') }}" alt="Adaptor / Kepala" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-amber-600 transition">Adaptor / Kepala</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Batok Charger & Fast Charging</span>
            </div>
        </div>

        <!-- 3. Adaptor 1Set & Car Charger -->
        <div onclick="selectProviderFilter('adaptor-set-car', 'Adaptor 1Set & Car Charger')" class="bg-white hover:bg-sky-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-sky-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-sky-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/charger-set.png') }}" alt="Adaptor 1Set & Car Charger" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-sky-600 transition">Adaptor 1Set & Car</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Charger Set & Charger Mobil</span>
            </div>
        </div>

        <!-- 4. Headset / Earphone -->
        <div onclick="selectProviderFilter('headset-earphone', 'Headset / Earphone')" class="bg-white hover:bg-purple-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-purple-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-purple-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/headset.png') }}" alt="Headset / Earphone" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-purple-600 transition">Headset / Earphone</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">TWS, Bluetooth, Earphone Kabel</span>
            </div>
        </div>

        <!-- 5. MMC & Flashdisk -->
        <div onclick="selectProviderFilter('mmc-flashdisk', 'MMC & Flashdisk')" class="bg-white hover:bg-emerald-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-emerald-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/MMC.png') }}" alt="MMC & Flashdisk" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-emerald-600 transition">MMC & Flashdisk</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">MicroSD & USB Flashdrive</span>
            </div>
        </div>

        <!-- 6. Powerbank -->
        <div onclick="selectProviderFilter('powerbank', 'Powerbank')" class="bg-white hover:bg-teal-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-teal-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-teal-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/powerbank.png') }}" alt="Powerbank" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-teal-600 transition">Powerbank</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Powerbank All Capacity</span>
            </div>
        </div>

        <!-- 7. Softcase -->
        <div onclick="selectProviderFilter('softcase', 'Softcase')" class="bg-white hover:bg-rose-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-rose-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/casing.png') }}" alt="Softcase" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-rose-600 transition">Softcase</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Casing, Softcase & Hardcase</span>
            </div>
        </div>

        <!-- 8. Antigores -->
        <div onclick="selectProviderFilter('antigores', 'Antigores')" class="bg-white hover:bg-blue-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-blue-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/antigores.png') }}" alt="Antigores" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-blue-600 transition">Antigores</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Tempered Glass & Hydrogel</span>
            </div>
        </div>

        <!-- 9. ACC Mix -->
        <div onclick="selectProviderFilter('acc-mix', 'ACC Mix')" class="bg-white hover:bg-fuchsia-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-fuchsia-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-fuchsia-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/ACC-MIX.png') }}" alt="ACC Mix" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-fuchsia-600 transition">ACC Mix</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Holder, Tripod & Aksesoris Lain</span>
            </div>
        </div>

    </div>

    <!-- LEVEL 2: CARDS SUB-HANDPHONE (NEW & SECOND DENGAN GAMBAR ICON) -->
    <div id="sub_handphone_grid" class="hidden grid grid-cols-2 gap-4">
        
        <!-- HP NEW / BARU -->
        <div onclick="selectProviderFilter('hp-new', 'Handphone Baru (New)')" class="bg-white hover:bg-sky-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-sky-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-sky-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/hp-baru.png') }}" alt="HP New (Baru)" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-sky-600 transition">HP New (Baru)</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Garansi Resmi & Segel Box</span>
            </div>
        </div>

        <!-- HP SECOND / BEKAS -->
        <div onclick="selectProviderFilter('hp-second', 'Handphone Second')" class="bg-white hover:bg-amber-50/40 p-5 rounded-3xl border border-slate-200/90 hover:border-amber-400 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col items-center justify-center text-center space-y-2.5 group active:scale-95">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-amber-50 flex items-center justify-center p-2.5 group-hover:scale-110 transition shadow-inner shrink-0">
                <img src="{{ asset('img/hp-scond.png') }}" alt="HP Second (Bekas)" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-gray-800 group-hover:text-amber-600 transition">HP Second (Bekas)</span>
                <span class="block text-[11px] text-gray-400 mt-0.5 font-medium">Unit Mulus & Bergaransi Toko</span>
            </div>
        </div>

    </div>

</div>