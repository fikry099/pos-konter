@extends('layouts.app')

@section('content')
<div class="space-y-3.5">
    
    <!-- HEADER HALAMAN -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-user-clock text-indigo-600 mr-2 text-lg sm:text-xl"></i> Manajemen Shift & Absensi Kasir
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 font-medium">Buka shift kerja, kelola modal awal, dan rekap penutupan kasir secara transparan.</p>
        </div>
    </div>

    <!-- FORM DYNAMIC (CLOSING ATAU OPENING) -->
    @if(isset($activeShift) && $activeShift)
        @include('shifts.partials.close-shift-form')
    @else
        @include('shifts.partials.open-shift-form')
    @endif

    <!-- TABEL RIWAYAT SHIFT -->
    @include('shifts.partials.history-table')

</div>
@endsection

@push('scripts')
    <!-- MODAL RESI SHIFT -->
    @include('shifts.partials.modal-shift-receipt')

    <!-- MODAL ABSENSI KARYAWAN SUSULAN (JIKA SHIFT SEDANG AKTIF) -->
    @if(isset($activeShift) && $activeShift)
        @include('shifts.partials.modal-join-shift')
    @endif
@endpush