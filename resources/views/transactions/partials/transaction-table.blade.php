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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[720px] text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-extrabold">
                        <th class="py-3 px-3 pl-4 whitespace-nowrap">Waktu & Nota</th>
                        <th class="py-3 px-3 whitespace-nowrap">Kasir / Shift</th>
                        <th class="py-3 px-3">Item Belanja / Target / Server</th>
                        <th class="py-3 px-3 text-center whitespace-nowrap">Metode & Status</th>
                        <th class="py-3 px-3 text-right whitespace-nowrap">Tagihan</th>
                        
                        @if(auth()->check() && auth()->user()->role === 'owner')
                            <th class="py-3 px-3 text-right whitespace-nowrap">Profit</th>
                        @endif
                        
                        <th class="py-3 px-3 text-center pr-4 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($transactions as $trx)
                        @php
                            $isCashOut = str_starts_with($trx->invoice_code, 'WD-') || 
                                         $trx->details->contains(fn($d) => str_contains(strtolower($d->custom_name ?? ''), 'tarik tunai'));
                            
                            $isCancelled = $trx->status === 'cancelled';
                        @endphp

                        <tr class="transition {{ $isCancelled ? 'bg-rose-50/70 border-l-4 border-l-rose-500' : ($isCashOut ? 'bg-amber-50/30 hover:bg-amber-50/50' : 'hover:bg-slate-50/70') }}">
                            
                            <td class="py-3 px-3 pl-4 whitespace-nowrap align-top">
                                <div class="font-mono font-bold text-xs flex items-center space-x-1.5 {{ $isCancelled ? 'text-rose-900 line-through' : 'text-slate-800' }}">
                                    <span>{{ $trx->invoice_code }}</span>
                                </div>
                                <div class="text-[10px] {{ $isCancelled ? 'text-rose-400' : 'text-slate-400' }} mt-0.5">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $trx->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>

                            <td class="py-3 px-3 whitespace-nowrap align-top">
                                @php
                                    $storeName = $trx->store->name 
                                        ?? $trx->user->store->name 
                                        ?? session('selected_store_name') 
                                        ?? 'Cabang';

                                    $accessoryStaffNames = $trx->details
                                        ->filter(function ($d) {
                                            if (empty($d->served_by_user_id) || !$d->servedBy) {
                                                return false;
                                            }

                                            $itemName = strtolower($d->custom_name ?? $d->product->name ?? '');
                                            $ignoredKeywords = ['voucher', 'pulsa', 'kuota', 'perdana', 'paket', 'top-up', 'topup', 'dana', 'gopay', 'ovo', 'shopee', 'linkaja', 'transfer', 'bank'];

                                            foreach ($ignoredKeywords as $keyword) {
                                                if (str_contains($itemName, $keyword)) {
                                                    return false;
                                                }
                                            }

                                            return true;
                                        })
                                        ->map(fn($d) => $d->servedBy->name)
                                        ->filter()
                                        ->unique()
                                        ->implode(', ');

                                    $cashierDisplay = !empty($accessoryStaffNames) ? $accessoryStaffNames : $storeName;
                                @endphp

                                <div class="font-bold {{ $isCancelled ? 'text-rose-800' : 'text-slate-800' }}">
                                    {{ $cashierDisplay }}
                                </div>
                                <span class="bg-slate-100 text-slate-600 text-[9px] px-1.5 py-0.5 rounded font-mono border border-slate-200 inline-block mt-1">
                                    Shift #{{ $trx->shift_id }}
                                </span>
                            </td>

                            <td class="py-3 px-3 align-top">
                                @php
                                    $allDetails = $trx->details ?? collect();
                                    $totalItemsCount = $allDetails->count();
                                    $limit = 2;
                                    $visibleDetails = $allDetails->take($limit);
                                    $remainingCount = $totalItemsCount - $limit;
                                @endphp

                                <div class="space-y-2 min-w-[220px]">
                                    @foreach($visibleDetails as $detail)
                                        @php
                                            $rawName = $detail->custom_name ? $detail->custom_name : ($detail->product->name ?? 'Produk');
                                            $serverBadge = $detail->digital_provider ?? '';
                                            $displayName = $rawName;
                                            $isTransferLabel = str_contains(strtolower($displayName), 'transfer') || str_contains(strtolower($displayName), 'top-up');
                                            
                                            if ($isCashOut) {
                                                $targetLabel = 'PENGIRIM/REK';
                                            } elseif ($isTransferLabel) {
                                                $targetLabel = 'TUJUAN/REK';
                                            } else {
                                                $targetLabel = 'NO';
                                            }
                                        @endphp

                                        <div class="text-xs">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="font-extrabold truncate max-w-[280px] {{ $isCancelled ? 'text-rose-900 line-through' : 'text-slate-800' }}">{{ $displayName }}</span>
                                                <span class="{{ $isCancelled ? 'text-rose-400' : 'text-slate-400' }} font-bold shrink-0">x{{ $detail->qty }}</span>
                                            </div>
                                            
                                            @if($detail->target_phone || !empty($serverBadge))
                                                <div class="flex flex-wrap items-center gap-1 mt-1">
                                                    @if($detail->target_phone)
                                                        <span class="bg-indigo-50 text-indigo-700 font-mono text-[9px] px-1.5 py-0.5 rounded border border-indigo-100 font-bold">
                                                            <i class="fa-solid fa-phone text-[8px] mr-0.5"></i>{{ $targetLabel }}: {{ $detail->target_phone }}
                                                        </span>
                                                    @endif

                                                    @if(!empty($serverBadge))
                                                        <span class="bg-purple-50 text-purple-700 font-bold text-[9px] px-1.5 py-0.5 rounded border border-purple-200 inline-flex items-center uppercase">
                                                            <i class="fa-solid fa-server text-[8px] mr-1 text-purple-500"></i>{{ $serverBadge }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if($remainingCount > 0)
                                        <button type="button" 
                                                onclick="showDetail({{ $trx->id }})" 
                                                class="inline-flex items-center gap-1 bg-slate-100 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 font-bold text-[10px] px-2 py-0.5 rounded-full border border-slate-200 hover:border-indigo-200 transition cursor-pointer mt-1">
                                            <i class="fa-solid fa-layer-group text-[9px]"></i>
                                            <span>+{{ $remainingCount }} item lainnya</span>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3 px-3 text-center whitespace-nowrap align-top">
                                @if($isCancelled)
                                    <span class="bg-rose-100 text-rose-700 border border-rose-300 font-black text-[9px] px-2 py-0.5 rounded-full inline-flex items-center space-x-1 shadow-2xs">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        <span>BATAL</span>
                                    </span>
                                    @if($trx->cancel_reason)
                                        <div class="text-[9px] font-bold text-rose-600 mt-1 italic max-w-[130px] mx-auto truncate" title="{{ $trx->cancel_reason }}">
                                            Ket: {{ $trx->cancel_reason }}
                                        </div>
                                    @endif
                                @else
                                    @if($isCashOut)
                                        <span class="bg-amber-50 text-amber-800 border border-amber-300 font-bold text-[9px] px-2 py-0.5 rounded-full inline-flex items-center space-x-1 shadow-2xs">
                                            <i class="fa-solid fa-money-bill-transfer text-[10px] text-amber-600"></i>
                                            <span>TARIK TUNAI</span>
                                        </span>
                                    @elseif(strtolower($trx->payment_method) === 'qris')
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[9px] px-2 py-0.5 rounded-full inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-qrcode text-[10px]"></i>
                                            <span>QRIS</span>
                                        </span>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[9px] px-2 py-0.5 rounded-full inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-money-bill-wave text-[10px]"></i>
                                            <span>TUNAI</span>
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <td class="py-3 px-3 text-right font-mono font-bold whitespace-nowrap align-top {{ $isCancelled ? 'text-rose-500 line-through' : 'text-indigo-700' }}">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>

                            @if(auth()->check() && auth()->user()->role === 'owner')
                                <td class="py-3 px-3 text-right font-mono text-xs font-bold whitespace-nowrap align-top {{ $isCancelled ? 'text-rose-400 line-through' : 'text-emerald-600' }}">
                                    {{ $isCancelled ? 'Rp 0' : '+Rp ' . number_format($trx->total_profit, 0, ',', '.') }}
                                </td>
                            @endif

                            <td class="py-3 px-3 text-center pr-4 whitespace-nowrap align-top">
                                <div class="flex items-center justify-center space-x-1">
                                    <button type="button" onclick="showDetail({{ $trx->id }})" class="bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-2 py-1 rounded-lg text-[11px] font-extrabold transition cursor-pointer inline-flex items-center space-x-1" title="Lihat Resi">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                        <span>Resi</span>
                                    </button>

                                    @if(auth()->check() && auth()->user()->role !== 'owner' && !$isCancelled)
                                        <button type="button" onclick="openCancelModal({{ $trx->id }}, '{{ $trx->invoice_code }}')" class="bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white p-1 rounded-lg text-[11px] font-extrabold transition cursor-pointer inline-flex items-center justify-center w-7 h-7" title="Batalkan Transaksi Ini">
                                            <i class="fa-solid fa-ban text-[11px]"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->role === 'owner' ? 7 : 6 }}" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-1 text-slate-300 block"></i>
                                <span class="text-xs font-bold text-slate-500">Belum ada data transaksi yang sesuai.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION CUSTOM LIGHT THEME (INLINE) -->
        <div class="p-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-slate-500">
            <div>
                Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
            </div>

            @if ($transactions->hasPages())
                {{-- Ditambahkan pr-12 sm:pr-16 agar posisi tombol bergeser ke kiri dari ikon AI --}}
                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1.5 pr-12 sm:pr-16">
                    {{-- Previous Page Link --}}
                    @if ($transactions->onFirstPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition active:scale-95 border border-slate-100">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($transactions->links()->elements as $element)
                        @if (is_string($element))
                            <span class="px-1 text-slate-400 font-bold">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $transactions->currentPage())
                                    <span class="w-9 h-9 flex items-center justify-center rounded-2xl bg-indigo-600 text-white font-black shadow-md shadow-indigo-200 text-xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition active:scale-95 border border-slate-100 text-xs">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition active:scale-95 border border-slate-100">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </span>
                    @endif
                </nav>
            @endif
        </div>
    </div>

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