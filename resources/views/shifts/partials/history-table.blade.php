<!-- KONTANER UTAMA DENGAN BOTTOM PADDING SUPAYA BISA DI-SCROLL KE BAWAH -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-16 pb-4">
    <!-- HEADER CARD -->
    <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
        <h2 class="font-black text-slate-800 text-xs sm:text-sm flex items-center">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mr-2.5 border border-indigo-100 shrink-0">
                <i class="fa-solid fa-clock-rotate-left text-xs sm:text-sm"></i>
            </span>
            <span>Riwayat & Ringkasan Pembukuan Shift</span>
        </h2>
    </div>

    <!-- WRAPPER SCROLL VERTIKAL & HORIZONTAL -->
    <div class="overflow-x-auto overflow-y-auto max-h-[350px] sm:max-h-[420px]">
        <table class="w-full text-left border-collapse min-w-[650px]">
            <!-- HEADER TABLE STICKY -->
            <thead class="sticky top-0 z-10 shadow-sm">
                <tr class="bg-slate-100 border-b border-slate-200 text-slate-500 uppercase font-extrabold text-[10px] sm:text-xs">
                    <th class="py-2.5 px-3.5">Kasir Bertugas</th>
                    <th class="py-2.5 px-3.5">Waktu Shift</th>
                    <th class="py-2.5 px-3.5 text-right">Modal Awal</th>
                    <th class="py-2.5 px-3.5 text-right">Uang Fisik Laci</th>
                    <th class="py-2.5 px-3.5 text-right">Selisih Kas</th>
                    <th class="py-2.5 px-3.5 text-center">Status</th>
                    <th class="py-2.5 px-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700 text-xs sm:text-sm bg-white">
                @forelse($shifts as $shift)
                    @php
                        $cashSales = $shift->transactions->where('payment_method', 'cash')->sum('total_price');
                        $qrisSales = $shift->transactions->where('payment_method', 'qris')->sum('total_price');
                        $expenses  = $shift->expenses ? $shift->expenses->sum('amount') : 0;
                        $expectedCash = $shift->cash_initial + $cashSales - $expenses;
                        $difference = $shift->cash_actual ? ($shift->cash_actual - $expectedCash) : 0;
                    @endphp
                    <tr class="hover:bg-indigo-50/40 transition">
                        <td class="py-3 px-3.5 font-bold text-slate-800 text-xs sm:text-sm">
                            {{ preg_replace('/\s*\([^)]*\)/', '', $shift->staff_names ?? ($shift->user->name ?? 'Kasir')) }}
                        </td>
                        <td class="py-3 px-3.5 text-slate-600 font-mono text-[11px] sm:text-xs">
                            <div class="font-bold text-slate-800">{{ $shift->start_time->format('d/m/Y H:i') }} WIB</div>
                            <div class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">
                                {{ $shift->end_time ? 's/d ' . $shift->end_time->format('H:i') . ' WIB' : 's/d Sekarang' }}
                            </div>
                        </td>
                        <td class="py-3 px-3.5 text-right font-mono text-slate-600 text-xs">
                            Rp {{ number_format($shift->cash_initial, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-800 text-xs">
                            {{ $shift->cash_actual ? 'Rp ' . number_format($shift->cash_actual, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-3.5 text-right font-mono font-bold text-xs">
                            @if(!$shift->end_time)
                                <span class="text-slate-400">-</span>
                            @elseif($difference == 0)
                                <span class="text-emerald-600">Rp 0 (Pas)</span>
                            @elseif($difference > 0)
                                <span class="text-emerald-600">+Rp {{ number_format($difference, 0, ',', '.') }}</span>
                            @else
                                <span class="text-rose-600">-Rp {{ number_format(abs($difference), 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-3.5 text-center">
                            @if($shift->status === 'open')
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full animate-pulse border border-emerald-200">Aktif</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 text-[10px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full border border-slate-200">Selesai</span>
                            @endif
                        </td>
                        <td class="py-3 px-3.5 text-center">
                            <!-- TOMBOL RESI SHIFT (DIBERI PROPERTI SUPAYA TIDAK TERTUTUP FLOATING WIDGET) -->
                            <button type="button" onclick="openShiftReceipt({{ json_encode([
                                'id'            => $shift->id,
                                'staff'         => preg_replace('/\s*\([^)]*\)/', '', $shift->staff_names ?? ($shift->user->name ?? 'Kasir')),
                                'start'         => $shift->start_time->format('d/m/Y H:i') . ' WIB',
                                'end'           => $shift->end_time ? $shift->end_time->format('d/m/Y H:i') . ' WIB' : 'Masih Berlangsung',
                                'initial'       => $shift->cash_initial,
                                'cash_sales'    => $cashSales,
                                'qris_sales'    => $qrisSales,
                                'expenses'      => $expenses,
                                'expected_cash' => $expectedCash,
                                'actual_cash'   => $shift->cash_actual ?? 0,
                                'difference'    => $difference,
                                'status'        => $shift->status
                            ]) }})" class="relative z-10 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-3 py-1.5 rounded-lg font-bold text-[11px] sm:text-xs transition flex items-center justify-center space-x-1 mx-auto border border-indigo-100 cursor-pointer shadow-sm active:scale-95">
                                <i class="fa-solid fa-receipt text-[10px] sm:text-xs"></i>
                                <span>Resi</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 text-xs sm:text-sm font-medium">
                            Belum ada riwayat pembukuan shift.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- EXTRA SPACER BOTTOM SUPAYA SCROLL HALAMAN LEBIH LEGA -->
<div class="h-16 w-full"></div>