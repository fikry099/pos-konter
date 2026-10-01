<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
    <div class="p-3.5 sm:p-4 border-b border-slate-100">
        <h2 class="text-xs sm:text-sm font-black text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-wallet text-indigo-600"></i>
            <span>Log Pengeluaran Kas Toko</span>
        </h2>
    </div>

    <div class="overflow-x-auto max-h-[420px] overflow-y-auto no-scrollbar flex-1">
        <table class="w-full text-left border-collapse">
            <thead class="sticky top-0 z-10">
                <tr class="bg-slate-50 text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200">
                    <th class="py-2.5 px-3">Waktu & Kasir</th>
                    <th class="py-2.5 px-3">Keterangan</th>
                    <th class="py-2.5 px-3 text-right">Jumlah Uang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-2.5 px-3 whitespace-nowrap">
                            <div class="font-bold text-slate-800 text-xs">{{ $expense->user->name ?? 'Kasir' }}</div>
                            <div class="text-[9px] font-mono text-slate-400">{{ $expense->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-2.5 px-3 text-slate-600 font-medium">
                            {{ $expense->description ?? '-' }}
                        </td>
                        <td class="py-2.5 px-3 font-mono font-bold text-rose-600 text-right whitespace-nowrap">
                            -Rp {{ number_format($expense->amount ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-slate-400">
                            <i class="fa-solid fa-receipt text-2xl mb-1 text-slate-300"></i>
                            <p class="text-xs font-bold text-slate-500">Belum ada pengeluaran kas yang dicatat.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>