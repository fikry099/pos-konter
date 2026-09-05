@extends('layouts.app')

@section('content')
<div class="space-y-3.5">
    
    <!-- 1. HEADER HALAMAN & METRICS CARD PENGELUARAN SHIFT AKTIF -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-wallet text-rose-600 mr-2 text-lg sm:text-xl"></i> Pengeluaran Kas Toko
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 font-medium">Pencatatan uang kas keluar selama shift (misal: operasional, konsumsi, atau perlengkapan).</p>
        </div>

        @if($activeShift)
            <div class="bg-rose-50/50 border border-rose-200/80 px-3.5 py-2.5 rounded-xl text-right flex items-center space-x-3 shrink-0 self-start sm:self-auto">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm shrink-0 border border-rose-200/60">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[10px] sm:text-[11px] text-rose-600 font-extrabold uppercase tracking-wider block">Pengeluaran Shift Aktif:</span>
                    <span class="text-sm sm:text-base font-black text-rose-700 font-mono block leading-tight">
                        Rp {{ number_format($activeShiftExpensesTotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- 2. GRID KONTEN DUA KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 items-start">

        <!-- SISI KIRI: FORM CATAT PENGELUARAN (COL-SPAN 4 / FULL DI TABLET) -->
        <div class="lg:col-span-4">
            @include('expenses.partials.create-form')
        </div>

        <!-- SISI KANAN: RIWAYAT DAFTAR PENGELUARAN (COL-SPAN 8 / FULL DI TABLET) -->
        <div class="lg:col-span-8">
            @include('expenses.partials.expense-table')
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // FUNGSI FORMAT ANGKA RIBUAN TITIK OTOMATIS
    function formatExpenseCurrency(input) {
        let rawValue = input.value.replace(/\D/g, '');
        
        document.getElementById('raw_expense_amount').value = rawValue;

        if (rawValue === '') {
            input.value = '';
            return;
        }

        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }

    // FUNGSI KONFIRMASI HAPUS MENGGUNAKAN SWEETALERT2
    function confirmDeleteExpense(deleteUrl, expenseName) {
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: `Catatan pengeluaran "${expenseName}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                container: 'z-[99999]'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = deleteUrl;
                form.method = 'POST';
                
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush