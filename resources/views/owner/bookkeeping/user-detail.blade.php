@extends('layouts.app')

@section('content')
<div class="space-y-4 max-w-4xl mx-auto">

    <!-- HEADER DETAIL & FILTER -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <!-- TOMBOL KEMBALI KE PEMBUKUAN -->
            <a href="{{ route('owner.bookkeeping', ['month' => $month, 'year' => $year]) }}" 
            class="w-9 h-9 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-xl flex items-center justify-center text-xs transition border border-slate-200 cursor-pointer" title="Kembali ke Pembukuan">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">Riwayat Absensi: {{ $user->name }}</h2>
                <p class="text-[11px] text-slate-400 font-medium">Batas Tepat Waktu: Shift Pagi ≤ 07.05 | Shift Sore ≤ 15.05</p>
            </div>
        </div>

        <!-- FILTER BULAN & TAHUN -->
        <form action="{{ route('owner.attendances.detail', $user->id) }}" method="GET" class="flex items-center space-x-2">
            <select name="month" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                @for($m = 1; $m <= 12; $m++)
                    @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mVal }}" {{ $month == $mVal ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>

            <select name="year" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                Filter
            </button>
        </form>
    </div>

    <!-- STATISTIK KEDISIPLINAN -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-emerald-50 border border-emerald-200/80 p-3.5 rounded-2xl flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-emerald-800 uppercase block">Hadir Tepat Waktu</span>
                <span class="text-lg font-black text-emerald-700">{{ $totalOnTime }} Kali</span>
            </div>
            <i class="fa-solid fa-circle-check text-2xl text-emerald-400"></i>
        </div>

        <div class="bg-rose-50 border border-rose-200/80 p-3.5 rounded-2xl flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-rose-800 uppercase block">Terlambat</span>
                <span class="text-lg font-black text-rose-700">{{ $totalLate }} Kali</span>
            </div>
            <i class="fa-solid fa-triangle-exclamation text-2xl text-rose-400"></i>
        </div>
    </div>

    <!-- TABEL RINCIAN ABSENSI -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-3.5 border-b border-slate-100 font-bold text-xs text-slate-800">
            Daftar Kehadiran Bulan {{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-extrabold">
                        <th class="py-2.5 px-3.5">Tanggal</th>
                        <th class="py-2.5 px-3.5">Shift</th>
                        <th class="py-2.5 px-3.5">Jam Masuk (Absen)</th>
                        <th class="py-2.5 px-3.5 text-center">Tipe Presensi</th>
                        <th class="py-2.5 px-3.5 text-center">Status Kedisiplinan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($attendances as $att)
                        @php
                            // Cek apakah karyawan ini yang membuka shift (user_id pertama di shift)
                            $isOpener = $att->shift && $att->shift->user_id == $att->user_id;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-3.5 font-mono text-slate-800">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                            <td class="py-2.5 px-3.5 capitalize font-bold text-slate-700">Shift {{ $att->shift_type }}</td>
                            <td class="py-2.5 px-3.5 font-mono font-bold text-slate-800">{{ $att->check_in }} WIB</td>
                            
                            <!-- BADGE TIPE PRESENSI (OPENER / JOINER) -->
                            <td class="py-2.5 px-3.5 text-center">
                                @if($isOpener)
                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold px-2 py-0.5 rounded-md text-[10px] inline-block">
                                        <i class="fa-solid fa-key text-[9px] mr-1"></i> Pembuka Shift
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 border border-slate-200 font-medium px-2 py-0.5 rounded-md text-[10px] inline-block">
                                        <i class="fa-solid fa-user-plus text-[9px] mr-1"></i> Susulan
                                    </span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3.5 text-center">
                                @if($att->is_on_time)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] inline-block">
                                        ⚡ Tepat Waktu
                                    </span>
                                @else
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] inline-block">
                                        Terlambat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 text-xs">
                                Belum ada catatan absensi untuk karyawan ini pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-slate-100 bg-slate-50">
            {{ $attendances->links() }}
        </div>
    </div>

</div>
@endsection