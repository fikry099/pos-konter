<div class="space-y-3.5">
    
    <!-- FILTER TABEL -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200/80">
        <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2.5">
            
            <!-- INPUT TANGGAL DENGAN IKON KALENDER DI KANAN BISA DIKLIK -->
            <div class="relative w-full sm:w-auto">
                <input type="date" id="expense_date_input" name="date" value="{{ request('date') }}" class="w-full pl-3 pr-9 py-2 bg-slate-50 text-slate-800 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer [color-scheme:light]">
                <button type="button" onclick="document.getElementById('expense_date_input').showPicker()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 transition cursor-pointer" title="Pilih Tanggal">
                    <i class="fa-solid fa-calendar-days text-xs"></i>
                </button>
            </div>

            <!-- DROPDOWN PILIH SHIFT -->
            <div class="w-full sm:flex-1">
                <select name="shift_id" class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                    <option value="">-- Semua Shift --</option>
                    @foreach($allShifts as $s)
                        <option value="{{ $s->id }}" {{ request('shift_id') == $s->id ? 'selected' : '' }}>
                            Shift #{{ $s->id }} ({{ $s->staff_names ?? $s->user->name }}) - {{ $s->start_time->format('d/m H:i') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- TOMBOL FILTER & RESET -->
            <div class="flex items-center space-x-2 w-full sm:w-auto shrink-0">
                <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs px-3.5 py-2 rounded-xl font-extrabold transition shadow-md shadow-indigo-200 flex items-center justify-center space-x-1.5 cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filter</span>
                </button>
                @if(request('date') || request('shift_id'))
                    <a href="{{ route('expenses.index') }}" class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-3 py-2 rounded-xl font-bold border border-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL PENGELUARAN -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[550px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-400 text-[10px] sm:text-xs uppercase font-extrabold">
                        <th class="py-2.5 px-3.5">Waktu</th>
                        <th class="py-2.5 px-3.5">Keterangan</th>
                        <th class="py-2.5 px-3.5">Kasir / Shift</th>
                        <th class="py-2.5 px-3.5 text-right">Nominal</th>
                        <th class="py-2.5 px-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm font-medium text-slate-700">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-rose-50/30 transition">
                            <td class="py-2.5 px-3.5 text-[11px] sm:text-xs text-slate-500 font-mono whitespace-nowrap">
                                {{ $exp->created_at->format('d/m/Y H:i') }} WIB
                            </td>
                            <td class="py-2.5 px-3.5 font-bold text-slate-800 text-xs sm:text-sm">
                                {{ $exp->description }}
                            </td>
                            <td class="py-2.5 px-3.5 text-xs whitespace-nowrap">
                                <div class="font-bold text-slate-700 text-xs">{{ $exp->user->name ?? '-' }}</div>
                                <span class="text-[10px] text-slate-400 font-mono border border-slate-200 bg-slate-50 px-1.5 py-0.5 rounded-md inline-block mt-0.5">Shift #{{ $exp->shift_id }}</span>
                            </td>
                            <td class="py-2.5 px-3.5 text-right font-mono font-black text-rose-600 text-xs sm:text-sm whitespace-nowrap">
                                Rp {{ number_format($exp->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center whitespace-nowrap">
                                <!-- GANTI DENGAN PEMANGGILAN FUNGSI SWEETALERT confirmDeleteExpense -->
                                <button type="button" onclick="confirmDeleteExpense('{{ route('expenses.destroy', $exp->id) }}', '{{ addslashes($exp->description) }}')" class="text-slate-400 hover:text-rose-600 w-7 h-7 rounded-lg hover:bg-rose-50 transition inline-flex items-center justify-center cursor-pointer" title="Hapus Pengeluaran">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-hand-holding-dollar text-3xl mb-2 text-slate-300 block"></i>
                                <span>Belum ada pengeluaran kas yang dicatat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100 bg-slate-50">
            {{ $expenses->links() }}
        </div>
    </div>

</div>