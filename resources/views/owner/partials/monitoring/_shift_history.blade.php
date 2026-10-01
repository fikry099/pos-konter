<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
    <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-xs sm:text-sm font-black text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-user-clock text-indigo-600"></i>
            <span>Riwayat Absensi & Audit Kasir</span>
        </h2>
    </div>

    <div class="overflow-x-auto max-h-[420px] overflow-y-auto no-scrollbar flex-1">
        <table class="w-full text-left border-collapse">
            <thead class="sticky top-0 z-10">
                <tr class="bg-slate-50 text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200">
                    <th class="py-2.5 px-3">Staff</th>
                    <th class="py-2.5 px-3">Jam Shift</th>
                    <th class="py-2.5 px-3">Selisih</th>
                    <th class="py-2.5 px-3 text-center">Status</th>
                    <th class="py-2.5 px-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                @forelse($shifts as $shift)
                    @php
                        $cashSales     = $shift->total_sales;
                        $shiftExpenses = $shift->total_expenses;
                        $expected      = $shift->calculateExpectedCash();
                        $actual        = $shift->cash_actual;
                        $difference    = $shift->status === 'closed' ? ($actual - $expected) : null;
                        $photoUrl      = $shift->photo_url ?? '';
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition cursor-pointer" onclick="showShiftDetail({
                        name: '{{ addslashes($shift->staff_names) }}',
                        date: '{{ $shift->created_at->format('d M Y, H:i') }} WIB',
                        closeDate: '{{ $shift->status === 'closed' ? $shift->updated_at->format('d M Y, H:i') . ' WIB' : 'Masih Aktif' }}',
                        startCash: 'Rp {{ number_format($shift->cash_initial ?? 0, 0, ',', '.') }}',
                        cashSales: 'Rp {{ number_format($cashSales, 0, ',', '.') }}',
                        expenses: 'Rp {{ number_format($shiftExpenses, 0, ',', '.') }}',
                        expected: 'Rp {{ number_format($expected, 0, ',', '.') }}',
                        actual: '{{ $shift->status === 'closed' ? 'Rp ' . number_format($actual, 0, ',', '.') : '-' }}',
                        difference: '{{ $shift->status === 'closed' ? 'Rp ' . number_format($difference, 0, ',', '.') : '-' }}',
                        photo: '{{ $photoUrl }}',
                        status: '{{ $shift->status }}'
                    })">
                        <td class="py-2.5 px-3">
                            <div class="font-extrabold text-slate-900 text-xs">{{ $shift->staff_names }}</div>
                            <div class="text-[9px] text-slate-400 font-bold">{{ $shift->created_at->format('d M Y') }}</div>
                        </td>

                        <td class="py-2.5 px-3 font-mono text-xs whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $shift->created_at->format('H:i') }}</span>
                            <span class="text-slate-400">&rsaquo;</span>
                            @if($shift->status === 'closed')
                                <span class="font-bold text-slate-800">{{ $shift->updated_at->format('H:i') }}</span>
                            @else
                                <span class="text-emerald-600 font-black">Aktif</span>
                            @endif
                        </td>

                        <td class="py-2.5 px-3 font-mono font-bold whitespace-nowrap">
                            @if($shift->status === 'closed')
                                @if($difference < 0)
                                    <span class="text-rose-600">Rp {{ number_format($difference, 0, ',', '.') }}</span>
                                @elseif($difference > 0)
                                    <span class="text-emerald-600">+Rp {{ number_format($difference, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-500">Rp 0</span>
                                @endif
                            @else
                                <span class="text-slate-400 font-normal">-</span>
                            @endif
                        </td>

                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $shift->status === 'closed' ? 'bg-slate-100 text-slate-600' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $shift->status === 'closed' ? 'Selesai' : 'Aktif' }}
                            </span>
                        </td>

                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            <span class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-2 py-1 rounded-lg text-[10px] font-extrabold transition inline-flex items-center space-x-1">
                                <i class="fa-solid fa-eye text-[9px]"></i>
                                <span>Detail</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            <i class="fa-solid fa-clock-rotate-left text-2xl mb-1 text-slate-300"></i>
                            <p class="text-xs font-bold text-slate-500">Belum ada riwayat shift kasir tercatat.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>