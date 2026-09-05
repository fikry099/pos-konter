@extends('layouts.app')

@section('content')
<div class="space-y-3.5">
    
    <!-- 1. HEADER HALAMAN & AKSI CETAK -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-cart-flatbed text-amber-500 mr-2 text-lg sm:text-xl"></i> Rekap Restok & Order Supplier
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Estimasi jumlah voucher & produk fisik yang harus dipesan agar kembali ke batas Stok Maksimum cabang.</p>
        </div>

        <!-- TOMBOL AKSI -->
        <div class="flex items-center shrink-0">
            <button type="button" onclick="printOrderReceipt()" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs px-4 py-2 rounded-xl font-extrabold transition shadow-md shadow-indigo-200 flex items-center space-x-1.5 whitespace-nowrap cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Cetak Nota Pesanan (PO)</span>
            </button>
        </div>
    </div>

    <!-- 2. SUB-KOMPONEN: CARDS RINGKASAN REKAP PO -->
    @include('products.partials.reorder-metrics')

    <!-- 3. SUB-KOMPONEN: TABEL REKOMENDASI DAN ADJUSTMENT ORDER -->
    @include('products.partials.reorder-table')

</div>
@endsection

@push('scripts')
    <!-- 4. SUB-KOMPONEN: AREA CETAK PO & SCRIPT JAVASCRIPT -->
    @include('products.partials.reorder-print')
@endpush