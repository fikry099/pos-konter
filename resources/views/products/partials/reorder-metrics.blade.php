@php
    $totalItems = $lowStockProducts->count();
    
    $initialPcs = $lowStockProducts->sum(function($stock) {
        $cur = (int) $stock->stock;
        $max = (int) ($stock->min_stock ?? 5);
        return max(1, $max - $cur);
    });

    $initialCost = $lowStockProducts->sum(function($stock) {
        $cur = (int) $stock->stock;
        $max = (int) ($stock->min_stock ?? 5);
        $qty = max(1, $max - $cur);
        return $qty * (float) $stock->product->cost_price;
    });
@endphp

<div class="grid grid-cols-2 gap-3">
    
    <!-- CARD 1: TOTAL JENIS PRODUK -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between transition hover:shadow-md min-w-0">
        <div class="space-y-0.5 min-w-0 flex-1">
            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block truncate">Total Jenis Produk</span>
            <span class="text-sm sm:text-lg font-black text-slate-900 font-mono block truncate">{{ $totalItems }} Item</span>
        </div>
        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-base shrink-0 shadow-sm border border-amber-100 ml-2">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <!-- CARD 2: TOTAL PCS DIORDER -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between transition hover:shadow-md min-w-0">
        <div class="space-y-0.5 min-w-0 flex-1">
            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block truncate">Total Pcs Diorder</span>
            <span id="grand_total_pcs" class="text-sm sm:text-lg font-black text-indigo-700 font-mono block truncate">{{ number_format($initialPcs, 0, ',', '.') }} Pcs</span>
        </div>
        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-base shrink-0 shadow-sm border border-indigo-100 ml-2">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>

    <!-- CARD 3: ESTIMASI MODAL PO -->
    <div class="col-span-2 bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between transition hover:shadow-md min-w-0">
        <div class="space-y-0.5 min-w-0 flex-1">
            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block truncate">Estimasi Total Modal PO</span>
            <span id="grand_total_cost" class="text-sm sm:text-lg font-black text-emerald-600 font-mono block truncate">Rp {{ number_format($initialCost, 0, ',', '.') }}</span>
        </div>
        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-base shrink-0 shadow-sm border border-emerald-100 ml-2">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>

</div>