@extends('layouts.app')

@section('content')
<div class="space-y-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-receipt text-indigo-600 mr-2 text-lg sm:text-xl"></i> Riwayat Transaksi Penjualan
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Lacak nota belanja, nomor HP pulsa, dan rekap omset per shift.</p>
        </div>

        <div class="grid {{ auth()->check() && auth()->user()->role === 'owner' ? 'grid-cols-2' : 'grid-cols-1' }} gap-2.5 w-full sm:w-auto shrink-0">
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

            @if(auth()->check() && auth()->user()->role === 'owner')
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
            @endif
        </div>
    </div>

    @include('transactions.partials.filter-form')

    @include('transactions.partials.transaction-table')

</div>

<div id="cancelTransactionModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 space-y-4 border border-slate-100 my-auto">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-black text-rose-600 text-sm flex items-center">
                <i class="fa-solid fa-triangle-exclamation mr-2 text-base"></i> Batalkan Transaksi
            </h4>
            <button type="button" onclick="closeCancelModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="cancelTransactionForm" onsubmit="submitCancelTransaction(event)">
            @csrf
            <input type="hidden" id="cancel_trx_id" name="trx_id">

            <div class="space-y-3">
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-2xl text-[11px] leading-relaxed">
                    <p class="font-bold">Perhatian!</p>
                    <p>Transaksi <span id="cancel_invoice_code" class="font-mono font-black">TRX-00000</span> akan dibatalkan. Stok barang fisik akan <span class="font-bold underline">dikembalikan otomatis</span> ke katalog.</p>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase mb-1">
                        Alasan Pembatalan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="cancel_reason_input" name="cancel_reason" required rows="3" 
                              placeholder="Contoh: Pelanggan salah produk / Uang kurang..." 
                              class="w-full text-xs font-medium p-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-rose-500 focus:bg-white focus:outline-none transition"></textarea>
                </div>
            </div>

            <div class="flex space-x-2 pt-3 border-t border-slate-100 mt-4">
                <button type="button" onclick="closeCancelModal()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-3 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btn_submit_cancel" class="w-1/2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center space-x-1 cursor-pointer">
                    <i class="fa-solid fa-ban"></i>
                    <span>Proses Batal</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
    @include('transactions.partials.modal-receipt')

    <script>
        function openCancelModal(trxId, invoiceCode) {
            document.getElementById('cancel_trx_id').value = trxId;
            document.getElementById('cancel_invoice_code').innerText = invoiceCode;
            document.getElementById('cancel_reason_input').value = '';
            document.getElementById('cancelTransactionModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelTransactionModal').classList.add('hidden');
        }

        function submitCancelTransaction(e) {
            e.preventDefault();

            let trxId = document.getElementById('cancel_trx_id').value;
            let reason = document.getElementById('cancel_reason_input').value.trim();

            if (!reason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Alasan Kosong!',
                    text: 'Harap isi alasan pembatalan transaksi.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            let btnSubmit = document.getElementById('btn_submit_cancel');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

            fetch(`/transactions/${trxId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cancel_reason: reason })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeCancelModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dibatalkan!',
                        text: data.message,
                        confirmButtonColor: '#4f46e5'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan sistem.',
                        confirmButtonColor: '#e11d48'
                    });
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memproses pembatalan.',
                    confirmButtonColor: '#e11d48'
                });
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Proses Batal';
            });
        }
    </script>
@endpush