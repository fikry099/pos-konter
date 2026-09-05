<!-- METRICS CARDS: RESPONSIVE GRID 2 KOLOM UNTUK TAB 600px -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    
    <!-- CARD 1: OMSET HARI INI -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">OMSET HARI INI</span>
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm sm:text-base shrink-0">
                <i class="fa-solid fa-cash-register"></i>
            </div>
        </div>
        <div>
            <div class="text-base sm:text-xl font-black text-slate-800 font-mono">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
            <span class="text-[9px] sm:text-[10px] text-slate-400 font-medium block mt-0.5">Penjualan tunai & QRIS</span>
        </div>
    </div>

    <!-- CARD 2: UNTUNG BERSIH HARI INI -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">UNTUNG BERSIH</span>
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm sm:text-base shrink-0">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
        <div>
            <div class="text-base sm:text-xl font-black text-emerald-600 font-mono">Rp {{ number_format($todayNetProfit, 0, ',', '.') }}</div>
            <span class="text-[9px] sm:text-[10px] text-slate-400 font-medium block mt-0.5">Dipotong kas ops</span>
        </div>
    </div>

    <!-- CARD 3: PENGELUARAN HARI INI -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">PENGELUARAN</span>
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm sm:text-base shrink-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
        <div>
            <div class="text-base sm:text-xl font-black text-rose-600 font-mono">Rp {{ number_format($todayExpenses, 0, ',', '.') }}</div>
            <span class="text-[9px] sm:text-[10px] text-slate-400 font-medium block mt-0.5">Operasional kasir</span>
        </div>
    </div>

    <!-- CARD 4: OMSET BULAN INI -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">OMSET BULAN INI</span>
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm sm:text-base shrink-0">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
        <div>
            <div class="text-base sm:text-xl font-black text-indigo-700 font-mono">Rp {{ number_format($monthSales, 0, ',', '.') }}</div>
            <span class="text-[9px] sm:text-[10px] text-indigo-600 font-extrabold block mt-0.5">Est: Rp {{ number_format($monthProfit, 0, ',', '.') }}</span>
        </div>
    </div>

</div>