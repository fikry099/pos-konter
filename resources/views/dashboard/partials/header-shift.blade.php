<div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <!-- JUDUL & DESKRIPSI -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Dashboard</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Ringkasan performa penjualan, keuangan, dan pertanggungjawaban shift kasir.</p>
    </div>
    
    <!-- BADGE SHIFT AKTIF -->
    <div class="flex items-center shrink-0">
        @if($activeShift)
            <div class="bg-slate-50 border border-slate-200/80 px-4 py-3 rounded-xl shadow-sm flex flex-wrap items-center gap-3 transition hover:border-slate-300 w-full lg:w-auto">
                <!-- IKON INDIKATOR STATUS HIJAU -->
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0 border border-emerald-100">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                
                <!-- DETAIL KASIR & MODAL -->
                <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm">
                    <span class="font-black text-slate-800 text-xs sm:text-sm">
                        {{ preg_replace('/\s*\([^)]*\)/', '', $activeShift->staff_names ?? $activeShift->user->name) }}
                    </span>
                    <span class="text-slate-300 hidden sm:inline">•</span>
                    <span class="text-slate-500 font-medium">
                        Modal: <strong class="font-mono text-indigo-600 font-black text-xs sm:text-sm">Rp {{ number_format($activeShift->cash_initial, 0, ',', '.') }}</strong>
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] sm:text-xs font-black bg-emerald-100 text-emerald-700 border border-emerald-200 ml-auto sm:ml-0">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span> Shift Aktif
                    </span>
                </div>
            </div>
        @else
            <div class="bg-slate-50 border border-slate-200/80 px-4 py-3 rounded-xl shadow-sm flex items-center space-x-3 text-xs sm:text-sm w-full lg:w-auto">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-moon"></i>
                </div>
                <span class="font-bold text-slate-500">Tidak Ada Shift Aktif</span>
            </div>
        @endif
    </div>
</div>