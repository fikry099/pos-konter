<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 space-y-3">
    <!-- HEADER -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center space-x-2">
            <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </span>
            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm">Transaksi Terakhir</h3>
        </div>
        <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center shrink-0">
            <span>Lihat Semua</span> <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
        </a>
    </div>

    <!-- WADAH SCROLLBAR (MAKSIMAL TINGGI 2 ITEM TRANSAKSI) -->
    <div class="max-h-[135px] overflow-y-auto space-y-2 pr-1 no-scrollbar">
        @forelse($latestTransactions as $trx)
            @php
                $firstDetail = $trx->details->first();
                $productName = $firstDetail && $firstDetail->product ? $firstDetail->product->name : 'Produk Custom';
                $otherCount  = $trx->details->count() - 1;
            @endphp
            <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between hover:border-indigo-200 transition">
                <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                    <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 text-xs shrink-0 shadow-2xs">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="font-mono font-bold text-xs text-slate-800 block truncate">{{ $trx->invoice_code }}</span>
                        <span class="text-[11px] text-slate-500 truncate block">
                            {{ $productName }} {{ $otherCount > 0 ? '+'.$otherCount.' item' : '' }}
                        </span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="font-mono font-black text-xs text-indigo-700 block">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-400 block">{{ $trx->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="text-center py-6 text-slate-400 text-xs font-medium">Belum ada transaksi recorded.</div>
        @endforelse
    </div>
</div>