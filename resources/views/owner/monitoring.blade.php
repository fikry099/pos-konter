@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- HEADER HALAMAN & TOTAL SELISIH -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-clipboard-user text-indigo-600 mr-2 text-lg sm:text-xl"></i> Monitoring & Audit Toko
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Pantau absensi karyawan dan selisih uang kasir secara real-time</p>
        </div>

        <!-- CARD RINGKASAN TOTAL SELISIH KAS BULAN INI -->
        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 flex items-center space-x-3 shrink-0">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-200/60 shrink-0">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Total Selisih (Bulan Ini)</p>
                <p class="text-sm sm:text-base font-black font-mono {{ $totalSelisihBulanIni < 0 ? 'text-rose-600' : ($totalSelisihBulanIni > 0 ? 'text-emerald-600' : 'text-slate-800') }}">
                    Rp {{ number_format($totalSelisihBulanIni ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- 1. STATUS SHIFT TOKO SAAT INI -->
    @include('owner.partials.monitoring._current_shift')

    <!-- 2. GRID 2 KOLOM (BERDAMPINGAN KANAN - KIRI) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
        <!-- SEBELAH KIRI: RIWAYAT ABSENSI & AUDIT KASIR -->
        @include('owner.partials.monitoring._shift_history')

        <!-- SEBELAH KANAN: LOG PENGELUARAN KAS TOKO -->
        @include('owner.partials.monitoring._expense_log')
    </div>

</div>

<!-- MODAL POPUP DETAIL AUDIT SHIFT -->
@include('owner.partials.monitoring._modal_shift_detail')

<script>
    function showShiftDetail(data) {
        document.getElementById('modal_kasir_name').innerText = data.name;
        document.getElementById('modal_start_date').innerText = data.date;
        document.getElementById('modal_close_date').innerText = data.closeDate;
        document.getElementById('modal_start_cash').innerText = data.startCash;
        document.getElementById('modal_cash_sales').innerText = data.cashSales;
        document.getElementById('modal_expenses').innerText = data.expenses;
        document.getElementById('modal_expected').innerText = data.expected;
        document.getElementById('modal_actual').innerText = data.actual;
        
        const diffEl = document.getElementById('modal_difference');
        diffEl.innerText = data.difference;
        
        const photoImg = document.getElementById('modal_photo_img');
        const photoEmpty = document.getElementById('modal_photo_empty');

        if (data.photo && data.photo.trim() !== '') {
            photoImg.src = data.photo;
            photoImg.classList.remove('hidden');
            photoEmpty.classList.add('hidden');
        } else {
            photoImg.src = '';
            photoImg.classList.add('hidden');
            photoEmpty.classList.remove('hidden');
        }

        document.getElementById('detail_shift_modal').classList.remove('hidden');
    }

    function closeShiftDetail() {
        document.getElementById('detail_shift_modal').classList.add('hidden');
    }
</script>
@endsection