<!-- MODAL POPUP RESTOK PRODUK BERANTAI (KHUSUS PRODUK FISIK) -->
<div id="restock_modal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden transition-all duration-300">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 transform transition-all max-h-[90vh] overflow-y-auto">
        
        <!-- HEADER MODAL -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
            <h3 class="text-base sm:text-lg font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-boxes-packing text-indigo-600 mr-2.5 text-lg"></i> Restok Barang Masuk
            </h3>
            <button type="button" onclick="closeRestockModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('products.restock.process') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- 1. KATEGORI UTAMA (LEVEL 1) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    1. Kategori Produk Fisik <span class="text-rose-500">*</span>
                </label>
                <select id="modal_main_category" onchange="handleModalMainCategoryChange()" class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                    <option value="">-- Pilih Kategori Utama --</option>
                    @foreach($categories as $parentCat)
                        @php
                            $catSlug = strtolower($parentCat->slug ?? '');
                            $catName = strtolower($parentCat->name ?? '');

                            $isDigital = str_contains($catSlug, 'pulsa') || str_contains($catName, 'pulsa') ||
                                         str_contains($catSlug, 'e-wallet') || str_contains($catName, 'ewallet') || str_contains($catName, 'e-wallet') || str_contains($catName, 'wallet') ||
                                         str_contains($catSlug, 'bank') || str_contains($catName, 'bank') || str_contains($catName, 'transfer') ||
                                         str_contains($catSlug, 'pln') || str_contains($catName, 'pln') || str_contains($catName, 'token') ||
                                         str_contains($catSlug, 'paket-data') || (str_contains($catName, 'paket') && !str_contains($catName, 'perdana'));
                        @endphp

                        @if(!$isDigital)
                            <option value="{{ $parentCat->id }}" data-name="{{ strtolower($parentCat->name) }}">
                                {{ $parentCat->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- 2. SUB-KATEGORI / PROVIDER (LEVEL 2) -->
            <div id="modal_sub_l1_container" class="hidden">
                <label id="modal_sub_l1_label" class="block text-xs font-bold text-indigo-900 mb-2">
                    2. Sub-Kategori / Provider <span class="text-rose-500">*</span>
                </label>
                <select id="modal_sub_l1_select" onchange="handleModalSubL1Change()" class="w-full bg-indigo-50/50 text-slate-800 border border-indigo-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    <option value="">-- Pilih Sub-Kategori / Provider --</option>
                    @foreach($categories as $parentCat)
                        @foreach($parentCat->children as $subCat1)
                            <option value="{{ $subCat1->id }}" data-parent="{{ $subCat1->parent_id }}" data-name="{{ strtolower($subCat1->name) }}" class="modal-sub-l1-option hidden">
                                {{ $subCat1->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <!-- 3. KATEGORI SPESIFIK / FINAL (LEVEL 3 - JIKA ADA) -->
            <div id="modal_sub_l2_container" class="hidden">
                <label class="block text-xs font-bold text-purple-900 mb-2">
                    3. Kategori Spesifik <span class="text-rose-500">*</span>
                </label>
                <select id="modal_sub_l2_select" onchange="handleModalSubL2Change()" class="w-full bg-purple-50/50 text-slate-800 border border-purple-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-purple-500 transition cursor-pointer">
                    <option value="">-- Pilih Kategori Spesifik --</option>
                    @foreach($categories as $parentCat)
                        @foreach($parentCat->children as $subCat1)
                            @foreach($subCat1->children as $subCat2)
                                <option value="{{ $subCat2->id }}" data-parent="{{ $subCat2->parent_id }}" data-name="{{ strtolower($subCat2->name) }}" class="modal-sub-l2-option hidden">
                                    {{ $subCat2->name }}
                                </option>
                            @endforeach
                        @endforeach
                    @endforeach
                </select>
            </div>

            <!-- 4. DROPDOWN PRODUK FISIK -->
            <div id="modal_product_container" class="hidden">
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Pilih Produk Fisik <span class="text-rose-500">*</span>
                </label>
                <select name="product_id" id="modal_product_select" onchange="updateProductStockBadge()" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-black text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                    <option value="" disabled selected>-- Pilih Produk Fisik --</option>
                    @foreach($stocks as $stockItem)
                        @php
                            $product = $stockItem->product;
                        @endphp
                        @if($product && $product->type === 'physical')
                            <option value="{{ $product->id }}" 
                                    data-stock="{{ $stockItem->stock }}"
                                    data-category-id="{{ $product->category_id }}"
                                    data-parent-id="{{ $product->category->parent_id ?? '' }}"
                                    data-grandparent-id="{{ $product->category->parent->parent_id ?? '' }}"
                                    class="modal-product-option hidden">
                                {{ $product->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- 5. INFORMASI BADGE STOK SAAT INI -->
            <div id="current_stock_badge_container" class="hidden bg-indigo-50/80 border border-indigo-100 rounded-2xl p-3.5 flex items-center justify-between transition">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-indigo-900 block uppercase tracking-wider">Stok Cabang Saat Ini</span>
                        <span class="text-[10px] text-slate-500 font-medium">Sisa fisik tersedia di sistem</span>
                    </div>
                </div>
                <div class="text-right">
                    <span id="current_stock_count" class="text-base sm:text-lg font-black text-indigo-600 font-mono">0</span>
                    <span class="text-xs font-bold text-indigo-900">Pcs</span>
                </div>
            </div>

            <!-- 6. JUMLAH TAMBAHAN STOK -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-2">Jumlah Barang Masuk (Pcs) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="number" name="qty_add" min="1" value="1" required placeholder="Contoh: 10" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-black text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-slate-400">Pcs</span>
                </div>
            </div>

            <!-- 7. CATATAN / KETERANGAN RESTOK -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-2">Keterangan / Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Pasokan dari distributor A / Nota #102" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <!-- TOMBOL SIMPAN -->
            <div class="flex items-center space-x-2.5 pt-2">
                <button type="button" onclick="closeRestockModal()" class="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold py-3 rounded-2xl text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-extrabold py-3 rounded-2xl text-xs transition shadow-lg shadow-indigo-600/30 flex items-center justify-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>Simpan Tambahan Stok</span>
                </button>
            </div>
        </form>
    </div>
</div>