<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- HEADER TABEL -->
    <div class="p-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center">
            <i class="fa-solid fa-list-check text-indigo-600 mr-2 text-xs"></i> Daftar Barang & Penyesuaian Kuantitas Order
        </span>
        <span class="text-[10px] sm:text-xs text-slate-400 font-medium">* Rumus: Jumlah Dipesan = Target Stok Maksimum - Sisa Stok</span>
    </div>

    <!-- WRAPPER SCROLL HORIZONTAL -->
    <div class="overflow-x-auto no-scrollbar">
        <table class="w-full text-left border-collapse min-w-[750px]" id="reorderTable">
            <thead>
                <tr class="bg-slate-50 text-[10px] sm:text-xs font-extrabold uppercase text-slate-400 border-b border-slate-200/80">
                    <th class="py-2.5 px-3">Kode / Nama Produk</th>
                    <th class="py-2.5 px-3">Kategori</th>
                    <th class="py-2.5 px-3 text-center">Sisa Stok</th>
                    <th class="py-2.5 px-3 text-center">Stok Maks. Target</th>
                    <th class="py-2.5 px-3">Harga Modal</th>
                    <th class="py-2.5 px-3 text-center w-32">Jumlah Dipesan</th>
                    <th class="py-2.5 px-3 text-right">Subtotal Modal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm font-medium text-slate-700">
                @forelse($lowStockProducts as $stockItem)
                    @php
                        $prod = $stockItem->product;
                        $currentStock = (int) $stockItem->stock;
                        $maxStock = (int) ($stockItem->min_stock ?? 5);
                        $saranOrder = max(1, $maxStock - $currentStock);
                        $initialSubtotal = $saranOrder * (float) $prod->cost_price;
                    @endphp
                    <tr class="hover:bg-indigo-50/40 transition item-row" 
                        data-id="{{ $prod->id }}"
                        data-name="{{ $prod->name }}"
                        data-code="{{ $prod->code }}"
                        data-cost="{{ (int)$prod->cost_price }}">
                        
                        <!-- KODE & NAMA PRODUK -->
                        <td class="py-3 px-3">
                            <div class="font-bold text-slate-800 text-xs sm:text-sm leading-snug">{{ $prod->name }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $prod->code ?? '-' }}</div>
                        </td>

                        <!-- KATEGORI DENGAN HIRARKI INDUK -->
                        <td class="py-3 px-3">
                            @php
                                $cat = $prod->category;
                                $parent = $cat ? $cat->parent : null;
                            @endphp
                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px] sm:text-xs border border-slate-200 inline-block">
                                @if($parent)
                                    <span class="text-slate-400 font-normal">{{ $parent->name }} &rsaquo;</span>
                                @endif
                                <span class="text-indigo-700 font-extrabold">{{ $cat->name ?? 'Tanpa Kategori' }}</span>
                            </span>
                        </td>

                        <!-- SISA STOK CABANG -->
                        <td class="py-3 px-3 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-xs inline-block whitespace-nowrap border border-rose-100">
                                {{ $currentStock }} Pcs
                            </span>
                        </td>

                        <!-- STOK MAKS TARGET -->
                        <td class="py-3 px-3 text-center font-mono text-xs sm:text-sm font-bold text-slate-800 whitespace-nowrap">
                            {{ $maxStock }} Pcs
                        </td>

                        <!-- HARGA MODAL -->
                        <td class="py-3 px-3 font-mono text-slate-500 text-xs whitespace-nowrap">
                            Rp {{ number_format($prod->cost_price, 0, ',', '.') }}
                        </td>
                        
                        <!-- INPUT ADJUSTMENT KUANTITAS ORDER -->
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center">
                                <input type="number" min="1" value="{{ $saranOrder }}" oninput="calculateTotals()" class="order-qty w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-xs sm:text-sm text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                            </div>
                        </td>

                        <!-- SUBTOTAL MODAL PER ITEM -->
                        <td class="py-3 px-3 text-right font-mono font-bold text-indigo-700 text-xs sm:text-sm item-subtotal whitespace-nowrap">
                            Rp {{ number_format($initialSubtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-circle-check text-3xl sm:text-4xl mb-2 text-emerald-500 block"></i>
                            <span class="font-bold text-slate-700 block text-xs sm:text-sm">Semua Stok Voucher & Produk Fisik Masih Penuh!</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Tidak ada produk yang berada di bawah target stok minimal saat ini.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>