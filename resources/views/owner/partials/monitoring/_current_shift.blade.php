<div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2.5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
        <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-wider flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-emerald-500 animate-ping' : 'bg-rose-500' }}"></span>
            <span>Status Shift Toko Saat Ini</span>
        </h2>
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeShift ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
            {{ $activeShift ? 'Toko Buka (Aktif)' : 'Toko Tutup' }}
        </span>
    </div>

    @if($activeShift)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <!-- KASIR BERTUGAS -->
            <div class="flex items-center space-x-2.5 bg-indigo-50/50 p-2.5 rounded-xl border border-indigo-100">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                    {{ strtoupper(substr($activeShift->staff_names ?? 'K', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-wider truncate">Kasir Bertugas</p>
                    <p class="font-extrabold text-slate-800 text-xs truncate">{{ $activeShift->staff_names }}</p>
                </div>
            </div>

            <!-- MODAL KAS AWAL -->
            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/60 flex items-center justify-between sm:block">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Modal Kas Awal</p>
                <p class="text-xs sm:text-sm font-black font-mono text-slate-800 sm:mt-0.5">
                    Rp {{ number_format($activeShift->cash_initial ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <!-- ESTIMASI KAS DI LACI -->
            <div class="bg-purple-50/50 p-2.5 rounded-xl border border-purple-100 flex items-center justify-between sm:block">
                <p class="text-[9px] font-bold text-purple-400 uppercase tracking-wider">Estimasi Kas di Laci</p>
                <p class="text-xs sm:text-sm font-black font-mono text-purple-700 sm:mt-0.5">
                    Rp {{ number_format(($activeShift->cash_initial ?? 0) + ($currentCashSales ?? 0) - ($currentExpenses ?? 0), 0, ',', '.') }}
                </p>
            </div>
        </div>
    @else
        <div class="py-3 text-center text-slate-400">
            <i class="fa-solid fa-store-slash text-xl mb-1 text-slate-300"></i>
            <p class="text-xs font-bold text-slate-500">Saat ini belum ada kasir yang membuka shift toko.</p>
        </div>
    @endif
</div>