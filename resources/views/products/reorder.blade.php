@extends('layouts.app')

@section('content')
<div class="space-y-3.5">
    
    <!-- 1. HEADER HALAMAN & AKSI CETAK -->
    @include('products.partials.reorder-header')

    <!-- 2. KOMPONEN FILTER KATEGORI UTAMA & SUB-KATEGORI DINAMIS -->
    @include('products.partials.reorder-filters')

    <!-- 3. CARDS RINGKASAN REKAP PO -->
    @include('products.partials.reorder-metrics')

    <!-- 4. TABEL REKOMENDASI DAN ADJUSTMENT ORDER -->
    @include('products.partials.reorder-table')

</div>
@endsection

@push('scripts')
    <!-- AREA CETAK PO & SCRIPT JAVASCRIPT GABUNGAN -->
    @include('products.partials.reorder-print')

    <!-- SCRIPT POPUP AUTOMATIS SWEETALERT2 -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('info') || session('warning') || session('error') || session('success'))
                Swal.fire({
                    icon: "{{ session('error') ? 'error' : (session('warning') ? 'warning' : (session('success') ? 'success' : 'info')) }}",
                    title: 'Informasi Restok',
                    text: "{{ session('info') ?? session('warning') ?? session('error') ?? session('success') }}",
                    confirmButtonColor: '#4f46e5',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl text-xs font-bold px-5 py-2.5'
                    }
                });
            @endif
        });
    </script>
@endpush