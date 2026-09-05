@extends('layouts.app')

@section('content')
<div class="space-y-4">
    
    <!-- 1. HEADER HALAMAN & RINGKASAN FINANCIAL -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <!-- JUDUL & DESKRIPSI -->
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-receipt text-indigo-600 mr-2 text-lg sm:text-xl"></i> Riwayat Transaksi Penjualan
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Lacak nota belanja, nomor HP pulsa, dan rekap omset per shift.</p>
        </div>

        <!-- METRICS CARD RINGKASAN OMSET & PROFIT -->
        <div class="grid grid-cols-2 gap-2.5 w-full sm:w-auto shrink-0">
            <!-- CARD TOTAL OMSET -->
            <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl flex items-center space-x-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wider truncate">Total Omset</span>
                    <span class="text-xs sm:text-sm font-black text-indigo-700 font-mono block truncate">
                        Rp {{ number_format($summary['total_omset'] ?? $summary['total_price'] ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- CARD TOTAL PROFIT -->
            <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl flex items-center space-x-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wider truncate">Total Profit</span>
                    <span class="text-xs sm:text-sm font-black text-emerald-600 font-mono block truncate">
                        Rp {{ number_format($summary['total_profit'] ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SUB-KOMPONEN: FILTER & PENCARIAN -->
    @include('transactions.partials.filter-form')

    <!-- 3. SUB-KOMPONEN: TABEL RIWAYAT TRANSAKSI -->
    @include('transactions.partials.transaction-table')

</div>
@endsection

{{-- DIPINDAHKAN KE LUAR SECTION CONTENT SUPAYA DI-RENDER DI TINGKAT TERATAS LAYOUT --}}
@push('scripts')
    @include('transactions.partials.modal-receipt')
@endpush