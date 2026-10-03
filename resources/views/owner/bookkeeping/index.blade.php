@extends('layouts.app')

@section('content')
<div class="space-y-4 pb-16">

    <!-- HEADER PAGE & FILTER PERIODE -->
    <div class="flex flex-col gap-3 bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100 shadow-inner">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-800">Pembukuan & Rekap Bonus</h2>
                    <p class="text-xs text-slate-400 font-medium">Laba/Rugi Bersih & Komisi Penjualan Aksesoris</p>
                </div>
            </div>

            <!-- TOMBOL EXPORT EXCEL -->
            <a href="{{ route('owner.bookkeeping.export', ['month' => $month, 'year' => $year]) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2.5 rounded-2xl text-xs font-extrabold transition shadow-sm flex items-center justify-center space-x-2 shrink-0 cursor-pointer">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Export Laporan Excel</span>
            </a>
        </div>

        <!-- FORM FILTER BULAN & TAHUN -->
        <form action="{{ route('owner.bookkeeping') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3 border-t border-slate-100">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <select name="month" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                    @for($m = 1; $m <= 12; $m++)
                        @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $mVal }}" {{ (string)$month === $mVal ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-calendar"></i>
                </div>
                <select name="year" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                    @php
                        $currentYear = (int)date('Y');
                        $startYear = min($currentYear - 3, (int)$year);
                    @endphp
                    @for($y = $currentYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-extrabold transition shadow-sm cursor-pointer flex items-center justify-center space-x-2 active:scale-95">
                <i class="fa-solid fa-filter text-[11px]"></i>
                <span>Terapkan Filter</span>
            </button>
        </form>
    </div>

    <!-- WIDGET RINGKASAN PEMBUKUAN KEUANGAN & PEMBAYARAN -->
    <div class="space-y-3">
        <!-- BARIS 1: WIDGET UTAMA (4 CARD RINGKASAN KEUANGAN) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- TOTAL OMSET -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div class="space-y-1 truncate">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Total Omset</span>
                    <div class="text-base sm:text-lg font-black text-slate-800 font-mono truncate" title="Rp {{ number_format($financialSummary['total_omset'] ?? 0, 0, ',', '.') }}">
                        Rp {{ number_format($financialSummary['total_omset'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-slate-400 block font-medium">Pendapatan Kotor</span>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-base shrink-0 border border-slate-200/60">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <!-- LABA KOTOR -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div class="space-y-1 truncate">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Laba Kotor (Gross)</span>
                    <div class="text-base sm:text-lg font-black text-indigo-600 font-mono truncate" title="Rp {{ number_format($financialSummary['gross_profit'] ?? 0, 0, ',', '.') }}">
                        Rp {{ number_format($financialSummary['gross_profit'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-slate-400 block font-medium">Omset − HPP Barang</span>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 border border-indigo-100">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>

            <!-- BEBAN & BONUS -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div class="space-y-1 truncate">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Beban & Bonus</span>
                    <div class="text-base sm:text-lg font-black text-rose-600 font-mono truncate" title="Rp {{ number_format(($financialSummary['total_expenses'] ?? 0) + ($financialSummary['total_bonus_allocation'] ?? 0), 0, ',', '.') }}">
                        Rp {{ number_format(($financialSummary['total_expenses'] ?? 0) + ($financialSummary['total_bonus_allocation'] ?? 0), 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-slate-500 font-medium truncate">
                        Ops: Rp {{ number_format($financialSummary['total_expenses'] ?? 0, 0, ',', '.') }} | Bonus: Rp {{ number_format($financialSummary['total_bonus_allocation'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-base shrink-0 border border-rose-100">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            <!-- LABA BERSIH OWNER -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 p-4 rounded-2xl text-white shadow-md shadow-indigo-600/20 flex items-center justify-between">
                <div class="space-y-1 truncate">
                    <span class="text-[10px] font-extrabold text-indigo-200 uppercase tracking-wider block">Laba Bersih (Net)</span>
                    <div class="text-base sm:text-lg font-black font-mono truncate" title="Rp {{ number_format($financialSummary['net_profit'] ?? 0, 0, ',', '.') }}">
                        Rp {{ number_format($financialSummary['net_profit'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-indigo-100 block font-medium">Bersih Diterima Owner</span>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-white/20 text-white backdrop-blur-sm flex items-center justify-center text-base shrink-0 border border-white/30">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>

        <!-- BARIS 2: RINCIAN SALDO MASUK (TUNAI vs QRIS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200/90 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-money-bill-wave text-emerald-600"></i> Total Uang Tunai (Laci Kasir)
                    </span>
                    <div class="text-base sm:text-xl font-black text-emerald-900 font-mono">
                        Rp {{ number_format($financialSummary['total_cash'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-emerald-700/80 font-medium block">Fisik uang wajib ada di laci konter</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>

            <div class="bg-purple-50/60 p-4 rounded-2xl border border-purple-200/90 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-extrabold text-purple-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode text-purple-600"></i> Total Saldo QRIS / Transfer Bank
                    </span>
                    <div class="text-base sm:text-xl font-black text-purple-900 font-mono">
                        Rp {{ number_format($financialSummary['total_qris'] ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-purple-700/80 font-medium block">Uang masuk langsung ke Rekening / E-Wallet</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg shrink-0 border border-purple-200">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL REKAP BONUS & PERFORMA PENJUALAN KARYAWAN -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-award text-indigo-600"></i>
                    <span>Rekapitulasi Bonus Aksesoris Karyawan</span>
                </h3>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Klik pada baris karyawan untuk melihat rincian absensi</p>
            </div>
            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-extrabold px-3 py-1 rounded-full border border-indigo-100 self-start sm:self-auto flex items-center gap-1.5">
                <i class="fa-solid fa-tag text-[9px]"></i> Rate Standard: Rp 1.000 / Pcs
            </span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[650px] text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-400 text-[10px] uppercase font-extrabold">
                        <th class="py-3 px-4">Nama Karyawan</th>
                        <th class="py-3 px-3 text-center">Kedisiplinan Absensi</th>
                        <th class="py-3 px-3 text-center">Qty Terjual</th>
                        <th class="py-3 px-3 text-right">Omset Aksesoris</th>
                        <th class="py-3 px-3 text-center">Rate Insentif</th>
                        <th class="py-3 px-4 text-right">Total Komisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($employeeReport as $emp)
                        <tr onclick="window.location.href='{{ route('owner.attendances.detail', ['user' => $emp['user_id'], 'month' => $month, 'year' => $year]) }}'" 
                            class="hover:bg-indigo-50/50 transition cursor-pointer group">
                            
                            <!-- NAMA KARYAWAN DENGAN AVATAR -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xs shrink-0 border border-indigo-200/60 group-hover:bg-indigo-600 group-hover:text-white transition">
                                        {{ strtoupper(substr($emp['name'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('owner.attendances.detail', ['user' => $emp['user_id'], 'month' => $month, 'year' => $year]) }}" class="font-extrabold text-slate-800 group-hover:text-indigo-600 text-xs transition block hover:underline">
                                            {{ $emp['name'] }}
                                        </a>
                                        <span class="text-[9px] text-slate-400 font-semibold uppercase block">{{ $emp['role'] }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- KEDISIPLINAN ABSENSI -->
                            <td class="py-3.5 px-3 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold px-2.5 py-1 rounded-xl text-[10px] flex items-center gap-1" title="Hadir Tepat Waktu">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
                                        <span>{{ $emp['total_ontime'] }}x Tepat</span>
                                    </span>
                                    @if(($emp['total_late'] ?? 0) > 0)
                                        <span class="bg-rose-50 text-rose-700 border border-rose-200/80 font-bold px-2.5 py-1 rounded-xl text-[10px] flex items-center gap-1" title="Terlambat">
                                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-[10px]"></i>
                                            <span>{{ $emp['total_late'] }}x Telat</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- QTY TERJUAL -->
                            <td class="py-3.5 px-3 text-center">
                                <span class="bg-slate-100 text-slate-700 font-black px-2.5 py-1 rounded-xl border border-slate-200 text-[11px]">
                                    {{ $emp['accessories_qty'] }} Pcs
                                </span>
                            </td>

                            <!-- OMSET AKSESORIS -->
                            <td class="py-3.5 px-3 text-right font-mono font-bold text-slate-800">
                                Rp {{ number_format($emp['accessories_omset'], 0, ',', '.') }}
                            </td>

                            <!-- RATE -->
                            <td class="py-3.5 px-3 text-center text-[11px] font-bold text-slate-500">
                                Rp {{ number_format($emp['bonus_rate'], 0, ',', '.') }}
                            </td>

                            <!-- TOTAL KOMISI -->
                            <td class="py-3.5 px-4 text-right font-mono font-black text-emerald-600 text-sm">
                                +Rp {{ number_format($emp['total_bonus'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
                                    <i class="fa-solid fa-users-slash"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-500 block">Belum ada data shift atau transaksi pada periode ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CARD ACTION: HISTORI RESTOK BARANG -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 hover:border-indigo-200 transition">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100 shadow-inner">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm">Histori Restok & Reorder Barang</h4>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    Estimasi Modal Restok Periode Ini: 
                    <span class="font-mono font-bold text-indigo-600">Rp {{ number_format($totalRestockCost ?? 0, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <a href="{{ route('owner.restock.history', ['month' => $month, 'year' => $year]) }}" 
           class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2.5 rounded-2xl text-xs font-extrabold transition shadow-sm flex items-center justify-center space-x-2 shrink-0 cursor-pointer">
            <i class="fa-solid fa-list-check text-xs"></i>
            <span>Buka Tabel Histori Restok</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

</div>
@endsection