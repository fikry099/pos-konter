<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto no-scrollbar">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-slate-50 text-[10px] sm:text-xs font-extrabold uppercase text-slate-400 border-b border-slate-200/80">
                    <th class="py-3 px-4">Kode / Nama Produk</th>
                    <th class="py-3 px-4">Kategori & Hirarki</th>
                    <th class="py-3 px-4 text-center">Jenis</th>
                    <th class="py-3 px-4 text-right">Harga Jual</th>
                    <th class="py-3 px-4 text-center">Stok Saat Ini</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Aksi Restok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm font-medium text-slate-700">
                @forelse($stocks as $stockItem)
                    @php
                        // Ambil model Product & Category dari relasi StoreProductStock
                        $product = $stockItem->product;
                        $cat = $product->category ?? null;
                        $parentCat = $cat?->parent ?? null;
                        $grandParentCat = $parentCat?->parent ?? null;
                    @endphp

                    <!-- PENYESUAIAN 1: Tambahkan data-id milik product -->
                    <tr class="product-row hover:bg-indigo-50/30 transition"
                        data-id="{{ $product->id }}"
                        data-category="{{ strtolower($cat->name ?? '') }}"
                        data-cat-slug="{{ strtolower($cat->slug ?? '') }}"
                        data-cat-name="{{ strtolower($cat->name ?? '') }}"
                        data-parent-slug="{{ strtolower($parentCat->slug ?? '') }}"
                        data-parent-name="{{ strtolower($parentCat->name ?? '') }}"
                        data-grand-parent-slug="{{ strtolower($grandParentCat->slug ?? '') }}"
                        data-name="{{ strtolower($product->name ?? '') }}"
                        data-code="{{ strtolower($product->code ?? '') }}">

                        <!-- KODE / NAMA PRODUK -->
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800 block text-xs sm:text-sm">{{ $product->name ?? '-' }}</span>
                            <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ $product->code ?? '-' }}</span>
                        </td>

                        <!-- KATEGORI & HIRARKI -->
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-[10px] sm:text-xs border border-indigo-100 inline-block">
                                @if($grandParentCat)
                                    <span class="text-slate-400 font-normal">{{ $grandParentCat->name }} &rsaquo;</span>
                                @endif
                                @if($parentCat)
                                    <span class="text-slate-400 font-normal">{{ $parentCat->name }} &rsaquo;</span>
                                @endif
                                <span>{{ $cat->name ?? 'Tanpa Kategori' }}</span>
                            </span>
                        </td>

                        <!-- JENIS PRODUK -->
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-black text-[10px] uppercase border border-emerald-100">
                                {{ strtoupper($product->type ?? 'FISIK') }}
                            </span>
                        </td>

                        <!-- HARGA JUAL -->
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800">
                            Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                        </td>

                        <!-- STOK SAAT INI (CABANG) -->
                        <td class="py-3.5 px-4 text-center">
                            <!-- PENYESUAIAN 2: Berikan id unik & data attribute pada badge stok -->
                            <span id="stock-badge-{{ $product->id }}" data-stock="{{ $stockItem->stock }}" class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 font-black text-xs font-mono inline-block border border-slate-200 transition-all duration-300">
                                <span id="stock-val-{{ $product->id }}">{{ $stockItem->stock }}</span> Pcs
                            </span>
                        </td>

                        <!-- STATUS -->
                        <td class="py-3.5 px-4 text-center">
                            @if($product->is_active)
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[10px] border border-emerald-100">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600 font-bold text-[10px] border border-rose-100">Nonaktif</span>
                            @endif
                        </td>

                        <!-- AKSI RESTOK -->
                        <td class="py-3.5 px-4 text-center">
                            <button type="button" 
                                    onclick="openRestockModal('{{ $product->id }}', '{{ $product->category_id }}', '{{ $parentCat->id ?? '' }}', '{{ $grandParentCat->id ?? '' }}')" 
                                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-[11px] font-extrabold px-3 py-1.5 rounded-xl shadow-xs transition flex items-center justify-center space-x-1 mx-auto cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Restok</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-boxes-packing text-3xl mb-2 text-slate-300 block"></i>
                            <span class="font-bold text-slate-700 text-xs sm:text-sm block">Belum ada stok produk fisik di cabang ini</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>