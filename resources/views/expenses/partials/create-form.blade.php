<div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 space-y-3.5">
    <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm flex items-center">
        <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mr-2 border border-rose-100 shrink-0">
            <i class="fa-solid fa-receipt text-xs"></i>
        </span>
        <span>Input Pengeluaran Baru</span>
    </h3>

    @if($activeShift)
        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-3">
            @csrf
            
            <!-- Hidden Input untuk Angka Murni ke Backend -->
            <input type="hidden" name="amount" id="raw_expense_amount" value="0">

            <div>
                <label class="block text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Pengeluaran</label>
                <input type="text" name="description" required placeholder="Misal: Air minum galon / plastik" class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:bg-white transition placeholder-slate-400">
            </div>

            <div>
                <label class="block text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal (Rp)</label>
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs font-black">
                        Rp
                    </div>
                    <!-- Input Masker Tampilan Titik Ribuan -->
                    <input type="text" id="formatted_expense_amount" oninput="formatExpenseCurrency(this)" required placeholder="10.000" autocomplete="off" class="w-full pl-9 pr-3 py-2 bg-slate-50 text-rose-600 border border-slate-200 rounded-xl text-xs sm:text-sm font-black focus:ring-2 focus:ring-rose-500 focus:bg-white transition placeholder-slate-400">
                </div>
            </div>

            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-extrabold py-2.5 rounded-xl text-xs transition shadow-md shadow-rose-200 flex items-center justify-center space-x-1.5 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Simpan Pengeluaran</span>
            </button>
        </form>
    @else
        <div class="bg-amber-50 text-amber-800 p-3.5 rounded-xl text-xs font-medium border border-amber-200/80 text-center space-y-1">
            <i class="fa-solid fa-lock text-lg mb-1 block text-amber-600"></i>
            <span class="font-bold text-xs block">Shift Belum Aktif</span>
            <span class="text-[11px] text-amber-700/80 block">Buka shift terlebih dahulu sebelum menginput pengeluaran kasir.</span>
        </div>
    @endif
</div>