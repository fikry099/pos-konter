<div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
    <form action="{{ route('transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        
        <!-- 1. INPUT CARI NOTA / NO HP -->
        <div class="md:col-span-3 space-y-1">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Cari Nota / No. HP</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode TRX / No HP..." 
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>
        </div>

        <!-- 2. INPUT FILTER TANGGAL (IKON BAWAAN BROWSER DI-HIDE) -->
        <div class="md:col-span-2 space-y-1">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Filter Tanggal</label>
            <div class="relative flex items-center">
                <input type="date" id="filter_date_input" name="date" value="{{ request('date') }}" 
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-9 py-2 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-inner-spin-button]:hidden">
                <button type="button" 
                        onclick="document.getElementById('filter_date_input').showPicker ? document.getElementById('filter_date_input').showPicker() : document.getElementById('filter_date_input').focus()" 
                        class="absolute right-3 text-indigo-600 hover:text-indigo-800 cursor-pointer p-1">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                </button>
            </div>
        </div>

        <!-- 3. SELECT FILTER SHIFT KERJA (DENGAN NAMA KASIR) -->
        <div class="md:col-span-2 space-y-1">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Shift Kerja</label>
            <select name="shift_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                <option value="">-- Semua Shift --</option>
                @if(isset($shifts))
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                            Shift #{{ $shift->id }} ({{ $shift->staff_names }})
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- 4. SELECT FILTER KATEGORI / KATALOG -->
        <div class="md:col-span-3 space-y-1">
            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Kategori / Katalog</label>
            <select name="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                <option value="">-- Semua Kategori --</option>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- 5. TOMBOL AKSI: FILTER & EXPORT EXCEL -->
        <div class="md:col-span-2 flex items-center space-x-2">
            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-2.5 rounded-xl transition flex items-center justify-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>

            <a href="{{ route('transactions.export_excel', request()->all()) }}" 
               class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-2.5 rounded-xl transition flex items-center justify-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Excel</span>
            </a>
        </div>

    </form>
</div>