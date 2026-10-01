<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3.5">
    <div class="border-b border-slate-100 pb-2.5">
        <h2 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
            <i class="fa-solid fa-square-plus text-rose-600 mr-1.5 text-sm"></i> Input Pengeluaran Baru
        </h2>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST" autocomplete="off" class="space-y-3">
        @csrf
        <input type="hidden" name="amount" id="raw_expense_amount" value="{{ old('amount') }}">
        
        <!-- HIDDEN INPUT UNTUK KATEGORI (DEFAULT: OPERATIONAL) -->
        <input type="hidden" name="category" id="expense_category_input" value="{{ old('category', 'operational') }}">

        <!-- SEGMENTED CONTROL / TOMBOL PILIHAN KIRI & KANAN -->
        <div class="space-y-1.5">
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
                Pilih Kategori Pengeluaran <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-2xl border border-slate-200">
                <!-- TOMBOL KIRI: OPERASIONAL -->
                <button type="button" 
                        id="btn_category_operational" 
                        onclick="selectExpenseCategory('operational')"
                        class="py-2.5 px-2 rounded-xl text-[11px] font-extrabold transition-all flex flex-col items-center justify-center space-y-0.5 cursor-pointer shadow-xs bg-rose-600 text-white">
                    <span class="flex items-center space-x-1">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>Operasional</span>
                    </span>
                    <span class="text-[9px] font-medium opacity-80">(Membayar vocer XL dll)</span>
                </button>

                <!-- TOMBOL KANAN: DIAMBIL OWNER -->
                <button type="button" 
                        id="btn_category_owner" 
                        onclick="selectExpenseCategory('owner_withdrawal')"
                        class="py-2.5 px-2 rounded-xl text-[11px] font-extrabold transition-all flex flex-col items-center justify-center space-y-0.5 cursor-pointer text-slate-600 hover:text-slate-800 hover:bg-slate-200/50">
                    <span class="flex items-center space-x-1">
                        <i class="fa-solid fa-user-tie text-xs"></i>
                        <span>Diambil Owner</span>
                    </span>
                    <span class="text-[9px] font-medium opacity-70">(Prive / Setoran)</span>
                </button>
            </div>
        </div>

        <!-- KETERANGAN / KEPERLUAN -->
        <div class="space-y-1">
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
                Keterangan Pengeluaran <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="description" value="{{ old('description') }}" required placeholder="Misal: Air minum galon / plastik" class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:bg-white focus:outline-none transition placeholder:text-slate-400">
        </div>

        <!-- NOMINAL (RP) -->
        <div class="space-y-1">
            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
                Nominal (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-slate-400 text-xs font-mono">Rp</span>
                <input type="text" id="formatted_expense_amount" oninput="formatExpenseCurrency(this)" required placeholder="10.000" class="w-full pl-9 pr-3 py-2 bg-slate-50 text-slate-900 border border-slate-200 rounded-xl text-xs font-bold font-mono focus:ring-2 focus:ring-rose-500 focus:bg-white focus:outline-none transition">
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 active:scale-98 text-white font-extrabold py-2.5 rounded-xl text-xs transition shadow-md shadow-rose-200 flex items-center justify-center space-x-1.5 cursor-pointer mt-1">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Simpan Pengeluaran</span>
        </button>
    </form>
</div>

<script>
    function selectExpenseCategory(val) {
        document.getElementById('expense_category_input').value = val;
        
        let btnOperational = document.getElementById('btn_category_operational');
        let btnOwner = document.getElementById('btn_category_owner');

        if (val === 'operational') {
            btnOperational.className = "py-2.5 px-2 rounded-xl text-[11px] font-extrabold transition-all flex flex-col items-center justify-center space-y-0.5 cursor-pointer shadow-xs bg-rose-600 text-white";
            btnOwner.className = "py-2.5 px-2 rounded-xl text-[11px] font-extrabold transition-all flex flex-col items-center justify-center space-y-0.5 cursor-pointer text-slate-600 hover:text-slate-800 hover:bg-slate-200/50";
        } else {
            btnOwner.className = "py-2.5 px-2 rounded-xl text-[11px] font-extrabold transition-all flex flex-col items-center justify-center space-y-0.5 cursor-pointer shadow-xs bg-purple-600 text-white";
            btnOperational.className = "py-2.5 px-2 rounded-xl text-[11px] font-extrabold transition-all flex flex-col items-center justify-center space-y-0.5 cursor-pointer text-slate-600 hover:text-slate-800 hover:bg-slate-200/50";
        }
    }

    // Restore posisi jika ada validasi form error
    document.addEventListener("DOMContentLoaded", function() {
        let currentCategory = document.getElementById('expense_category_input').value;
        if (currentCategory) {
            selectExpenseCategory(currentCategory);
        }
    });
</script>