<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 space-y-3">
    <!-- HEADER -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-black text-slate-800 text-xs sm:text-sm flex items-center">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs mr-2.5 border border-indigo-100 shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </span>
            <span>Rekap Shift Terakhir</span>
        </h3>
        <a href="{{ auth()->user()->isOwner() ? route('owner.monitoring') : route('shifts.index') }}" 
           class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-xl transition flex items-center space-x-1 shrink-0">
            <span>{{ auth()->user()->isOwner() ? 'Pantau Shift' : 'Kelola Shift' }}</span>
            <i class="fa-solid fa-arrow-right text-[9px]"></i>
        </a>
    </div>

    <!-- WADAH TABEL DENGAN SCROLLBAR (MAKSIMAL TINGGI 2 BARIS DATA) -->
    <div class="overflow-x-auto no-scrollbar max-h-[110px] overflow-y-auto">
        <table class="w-full text-left border-collapse min-w-[600px] text-xs">
            <thead class="sticky top-0 bg-white z-10">
                <tr class="bg-slate-50 text-slate-400 uppercase font-extrabold text-[10px] border-b border-slate-200/60">
                    <th class="py-2.5 px-3">Kasir / Tim</th>
                    <th class="py-2.5 px-3">Waktu Shift</th>
                    <th class="py-2.5 px-3">Modal Awal</th>
                    <th class="py-2.5 px-3">Uang Fisik</th>
                    <th class="py-2.5 px-3 text-right">Selisih Kas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($recentShifts as $shift)
                    <tr class="hover:bg-indigo-50/40 transition">
                        <!-- 1. KASIR / TIM -->
                        <td class="py-2.5 px-3 font-bold text-slate-800 text-xs">
                            {{ preg_replace('/\s*\([^)]*\)/', '', $shift->staff_names ?? $shift->user->name) }}
                        </td>
                        
                        <!-- 2. WAKTU SHIFT -->
                        <td class="py-2.5 px-3 text-slate-600 font-mono text-[11px] whitespace-nowrap">
                            <div class="font-bold text-slate-800">
                                {{ $shift->start_time->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                @if($shift->status === 'open')
                                    <span class="text-indigo-600 font-bold">
                                        {{ $shift->start_time->format('H:i') }}
                                    </span> 
                                    <span class="text-slate-400">s/d</span> 
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded text-[10px] border border-emerald-100 animate-pulse inline-block">
                                        Aktif (<span class="live-clock">--:--:--</span>)
                                    </span>
                                @else
                                    {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time ? $shift->end_time->format('H:i') : '-' }} WIB
                                @endif
                            </div>
                        </td>

                        <!-- 3. MODAL AWAL -->
                        <td class="py-2.5 px-3 font-mono text-slate-600 text-xs whitespace-nowrap">
                            Rp {{ number_format($shift->cash_initial, 0, ',', '.') }}
                        </td>

                        <!-- 4. UANG FISIK (ACTUAL) -->
                        <td class="py-2.5 px-3 font-mono text-slate-800 text-xs whitespace-nowrap">
                            {{ $shift->cash_actual !== null ? 'Rp '.number_format($shift->cash_actual, 0, ',', '.') : '-' }}
                        </td>

                        <!-- 5. SELISIH KAS -->
                        <td class="py-2.5 px-3 text-right font-mono text-xs whitespace-nowrap">
                            @if($shift->status === 'open')
                                <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-100 inline-block">Berjalan</span>
                            @else
                                <span class="{{ $shift->difference < 0 ? 'text-rose-600 font-bold' : ($shift->difference > 0 ? 'text-blue-600 font-bold' : 'text-slate-600 font-bold') }}">
                                    Rp {{ number_format($shift->difference, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400 font-medium text-xs">Belum ada riwayat shift terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>