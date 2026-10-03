@extends('layouts.app')

@section('content')
<div class="space-y-4 w-full pb-16">

    <!-- HEADER DETAIL & FILTER -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3.5">
            <!-- TOMBOL KEMBALI KE PEMBUKUAN -->
            <a href="{{ route('owner.bookkeeping', ['month' => $month, 'year' => $year]) }}" 
               class="w-10 h-10 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-2xl flex items-center justify-center text-xs transition border border-slate-200/80 cursor-pointer shrink-0 shadow-xs" title="Kembali ke Pembukuan">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">Riwayat Absensi: {{ $user->name }}</h2>
                <p class="text-xs text-slate-400 font-medium">Batas Tepat Waktu: Shift Pagi ≤ 07.05 | Shift Sore ≤ 15.05</p>
            </div>
        </div>

        <!-- FILTER BULAN & TAHUN -->
        <form action="{{ route('owner.attendances.detail', $user->id) }}" method="GET" class="flex items-center space-x-2">
            <select name="month" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                @for($m = 1; $m <= 12; $m++)
                    @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mVal }}" {{ $month == $mVal ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>

            <select name="year" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2 rounded-xl text-xs font-extrabold transition shadow-sm cursor-pointer">
                Filter
            </button>
        </form>
    </div>

    <!-- STATISTIK KEDISIPLINAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="bg-emerald-50/70 border border-emerald-200/90 p-4 rounded-2xl flex items-center justify-between shadow-xs">
            <div class="space-y-0.5">
                <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block">Hadir Tepat Waktu</span>
                <span class="text-xl font-black text-emerald-900 font-mono">{{ $totalOnTime }} Kali</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-rose-50/70 border border-rose-200/90 p-4 rounded-2xl flex items-center justify-between shadow-xs">
            <div class="space-y-0.5">
                <span class="text-[10px] font-extrabold text-rose-800 uppercase tracking-wider block">Terlambat</span>
                <span class="text-xl font-black text-rose-900 font-mono">{{ $totalLate }} Kali</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0 border border-rose-200">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- TABEL RINCIAN ABSENSI -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 font-bold text-xs sm:text-sm text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-calendar-check text-indigo-600"></i>
            <span>Daftar Kehadiran Bulan {{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}</span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-400 text-[10px] uppercase font-extrabold">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-3">Shift</th>
                        <th class="py-3 px-3">Jam Masuk (Absen)</th>
                        <th class="py-3 px-3 text-center">Tipe Presensi</th>
                        <th class="py-3 px-4 text-center">Status Kedisiplinan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($attendances as $att)
                        @php
                            $isOpener = $att->shift && $att->shift->user_id == $att->user_id;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                            <td class="py-3 px-3 capitalize font-bold text-slate-700">Shift {{ $att->shift_type }}</td>
                            <td class="py-3 px-3 font-mono font-bold text-slate-800">{{ $att->check_in }} WIB</td>
                            
                            <!-- BADGE TIPE PRESENSI (OPENER / JOINER) -->
                            <td class="py-3 px-3 text-center">
                                @if($isOpener)
                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-bold px-2.5 py-1 rounded-xl text-[10px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-key text-[9px]"></i> Pembuka Shift
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 border border-slate-200/80 font-medium px-2.5 py-1 rounded-xl text-[10px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-user-plus text-[9px]"></i> Susulan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center">
                                @if($att->is_on_time)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold px-3 py-1 rounded-full text-[10px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i> Tepat Waktu
                                    </span>
                                @else
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200/80 font-bold px-3 py-1 rounded-full text-[10px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-[10px]"></i> Terlambat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs font-bold">
                                Belum ada catatan absensi untuk karyawan ini pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-3 border-t border-slate-100 bg-slate-50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection