@extends('layouts.app')

@push('styles')
<!-- CDN SWEETALERT2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="w-full bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80 space-y-4">
    <!-- HEADER HALAMAN DENGAN TOMBOL KEMBALI DI KIRI -->
    <div class="pb-3.5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-indigo-600 border border-slate-200 flex items-center justify-center transition shrink-0 cursor-pointer">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-sm sm:text-base font-black text-slate-800">Edit Produk: {{ $product->name }}</h2>
                <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Perbarui informasi produk fisik atau digital konter.</p>
            </div>
        </div>
    </div>

    <!-- FORM UTAMA EDIT PRODUK -->
    <form id="edit_product_form" action="{{ route('products.update', $product->id) }}" method="POST" onsubmit="validateEditProductForm(event)" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- HIDDEN INPUT ANGKA MURNI HARGA MODAL & HARGA JUAL -->
        <input type="hidden" name="cost_price" id="raw_cost_price" value="{{ (int)$product->cost_price }}">
        <input type="hidden" name="selling_price" id="raw_selling_price" value="{{ (int)$product->selling_price }}">
        
        <!-- HIDDEN INPUT FINAL KATEGORI ID YANG DISIMPAN KE DATABASE -->
        <input type="hidden" name="category_id" id="final_category_id" value="{{ $product->category_id }}">

        <!-- BARIS 1: KATEGORI UTAMA, NAMA PRODUK, KODE SKU (GRID 3 KOLOM) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <!-- LEVEL 1: KATEGORI UTAMA -->
            <div>
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1.5">Kategori Utama <span class="text-rose-500">*</span></label>
                <select id="main_category_select" onchange="handleMainCategoryChange()" required class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                    <option value="">-- Pilih Kategori Utama --</option>
                    @foreach($categories as $parentCat)
                        <option value="{{ $parentCat->id }}" data-slug="{{ strtolower($parentCat->slug ?? '') }}" data-name="{{ strtolower($parentCat->name) }}">
                            {{ $parentCat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- NAMA PRODUK -->
            <div>
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ $product->name }}" required placeholder="Contoh: TWS Bluetooth M10" class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white transition placeholder-slate-400">
            </div>

            <!-- KODE SKU / BARCODE -->
            <div>
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1.5">Kode SKU / Barcode <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <div class="relative rounded-xl shadow-sm">
                    <input type="text" name="code" value="{{ $product->code }}" placeholder="Auto-generate" class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-9 py-2.5 text-xs sm:text-sm font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition placeholder-slate-400">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-barcode text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEVEL 2: SUB-KATEGORI LEVEL 1 / PROVIDER -->
        <div id="sub_level1_container" class="hidden space-y-1.5 p-3.5 bg-indigo-50/50 rounded-xl border border-indigo-100 transition-all">
            <label id="sub_level1_label" class="block text-[10px] sm:text-xs font-bold text-indigo-900 uppercase tracking-wider">Sub-Kategori / Provider <span class="text-rose-500">*</span></label>
            <select id="sub_level1_select" onchange="handleSubLevel1Change()" class="w-full bg-white text-slate-800 border border-indigo-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                <option value="">-- Pilih Sub-Kategori --</option>
                @foreach($categories as $parentCat)
                    @foreach($parentCat->children as $subCat1)
                        <option value="{{ $subCat1->id }}" data-parent="{{ $parentCat->id }}" data-name="{{ strtolower($subCat1->name) }}" class="sub-l1-option hidden">
                            {{ $subCat1->name }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <!-- LEVEL 3: SUB-KATEGORI FINAL / SPESIFIK -->
        <div id="sub_level2_container" class="hidden space-y-1.5 p-3.5 bg-purple-50/50 rounded-xl border border-purple-100 transition-all">
            <label class="block text-[10px] sm:text-xs font-bold text-purple-900 uppercase tracking-wider">Kategori Spesifik / Final <span class="text-rose-500">*</span></label>
            <select id="sub_level2_select" onchange="handleSubLevel2Change()" class="w-full bg-white text-slate-800 border border-purple-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-purple-500 transition cursor-pointer">
                <option value="">-- Pilih Kategori Final --</option>
                @foreach($categories as $parentCat)
                    @foreach($parentCat->children as $subCat1)
                        @foreach($subCat1->children as $subCat2)
                            <option value="{{ $subCat2->id }}" data-parent="{{ $subCat1->id }}" data-name="{{ strtolower($subCat2->name) }}" class="sub-l2-option hidden">
                                {{ $subCat2->name }}
                            </option>
                        @endforeach
                    @endforeach
                @endforeach
            </select>
        </div>

        <!-- BARIS 2: JENIS PRODUK -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700">Jenis Produk <span class="text-rose-500">*</span></label>
                <span id="auto_detect_badge" class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 hidden">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Terdeteksi Otomatis
                </span>
            </div>
            <select name="type" id="product_type" onchange="toggleStockFields()" required class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                <option value="digital" {{ $product->type === 'digital' ? 'selected' : '' }}>Digital (Pulsa / Paket Data / E-Wallet / Transfer Bank)</option>
                <option value="physical" {{ $product->type === 'physical' ? 'selected' : '' }}>Fisik (Aksesoris / Voucher Fisik / Perdana)</option>
            </select>
        </div>

        <!-- BARIS 3: CARD HARGA MODAL & HARGA JUAL -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <!-- CARD HARGA MODAL -->
            <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50/40 flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1">Harga Modal (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">Rp</div>
                        <input type="text" id="formatted_cost_price" value="{{ number_format((int)$product->cost_price, 0, ',', '.') }}" oninput="formatProductCurrency(this, 'raw_cost_price')" required autocomplete="off" class="w-full pl-9 pr-3 py-2 bg-white text-slate-800 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                </div>
            </div>

            <!-- CARD HARGA JUAL -->
            <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50/40 flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">Rp</div>
                        <input type="text" id="formatted_selling_price" value="{{ number_format((int)$product->selling_price, 0, ',', '.') }}" oninput="formatProductCurrency(this, 'raw_selling_price')" required autocomplete="off" class="w-full pl-9 pr-3 py-2 bg-white text-slate-800 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- INPUT STOK (KHUSUS BARANG FISIK) -->
        <div id="stock_fields" class="{{ $product->type === 'physical' ? 'grid' : 'hidden' }} grid-cols-1 sm:grid-cols-2 gap-3.5 bg-indigo-50/40 p-3.5 rounded-xl border border-indigo-100">
            <div>
                <label class="block text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $product->current_store_stock ?? $product->stock ?? 0) }}" min="0" class="w-full bg-white text-slate-800 border border-slate-300 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batas Minimum Stok (Alert) <span class="text-rose-500">*</span></label>
                <input type="number" name="min_stock" value="{{ old('min_stock', $product->current_min_stock ?? $product->min_stock ?? 5) }}" min="0" class="w-full bg-white text-slate-800 border border-slate-300 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition">
            </div>
        </div>

        <!-- CHECKBOX AKTIFKAN PRODUK -->
        <div class="pt-1">
            <label class="flex items-start space-x-2.5 cursor-pointer">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded-md border-slate-300 focus:ring-indigo-500 cursor-pointer mt-0.5">
                <div>
                    <span class="text-[11px] sm:text-xs font-bold text-slate-800 block">Aktifkan produk agar langsung muncul di halaman POS kasir</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block mt-0.5">Jika tidak dicentang, produk akan disimpan sebagai draft.</span>
                </div>
            </label>
        </div>

        <!-- FOOTER AKSI DENGAN TOMBOL HAPUS DI KIRI DAN TOMBOL BATAL/UPDATE DI KANAN -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <!-- TOMBOL HAPUS PRODUK (SISI KIRI) -->
            <button type="button" onclick="confirmDeleteProduct()" class="bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200/80 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-trash-can text-xs"></i>
                <span>Hapus Produk</span>
            </button>

            <!-- TOMBOL BATAL & PERBARUI (SISI KANAN) -->
            <div class="flex items-center space-x-2.5">
                <a href="{{ route('products.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 sm:px-5 py-2.5 rounded-xl text-xs font-bold border border-slate-200 transition">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs font-extrabold transition shadow-md shadow-indigo-200 flex items-center cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate mr-1.5"></i> Perbarui Produk
                </button>
            </div>
        </div>
    </form>

    <!-- FORM HIDDEN UNTUK PENGHAPUSAN PRODUK -->
    <form id="delete_product_form" action="{{ route('products.destroy', $product->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<!-- CDN SWEETALERT2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const categoriesData = @json($categories);
    const currentCategoryId = "{{ $product->category_id }}";

    document.addEventListener('DOMContentLoaded', function() {
        initCategoryEditForm();
    });

    // FUNGSI KONFIRMASI HAPUS DENGAN SWEETALERT2
    function confirmDeleteProduct() {
        Swal.fire({
            title: 'Hapus Produk Ini?',
            text: "Produk '{{ $product->name }}' akan dihapus permanen dari sistem katalog.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Sekarang',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl font-sans',
                confirmButton: 'rounded-xl px-4 py-2 font-bold',
                cancelButton: 'rounded-xl px-4 py-2 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_product_form').submit();
            }
        });
    }

    function initCategoryEditForm() {
        if (!currentCategoryId) return;

        let foundMainId = null;
        let foundL1Id = null;
        let foundL2Id = null;

        categoriesData.forEach(parent => {
            if (parent.id == currentCategoryId) {
                foundMainId = parent.id;
            }
            if (parent.children) {
                parent.children.forEach(l1 => {
                    if (l1.id == currentCategoryId) {
                        foundMainId = parent.id;
                        foundL1Id = l1.id;
                    }
                    if (l1.children) {
                        l1.children.forEach(l2 => {
                            if (l2.id == currentCategoryId) {
                                foundMainId = parent.id;
                                foundL1Id = l1.id;
                                foundL2Id = l2.id;
                            }
                        });
                    }
                });
            }
        });

        if (foundMainId) {
            let mainSelect = document.getElementById('main_category_select');
            mainSelect.value = foundMainId;
            handleMainCategoryChange();

            if (foundL1Id) {
                let subL1Select = document.getElementById('sub_level1_select');
                subL1Select.value = foundL1Id;
                handleSubLevel1Change();

                if (foundL2Id) {
                    let subL2Select = document.getElementById('sub_level2_select');
                    subL2Select.value = foundL2Id;
                    handleSubLevel2Change();
                }
            }
        }
    }

    function handleMainCategoryChange() {
        let mainSelect = document.getElementById('main_category_select');
        let selectedOption = mainSelect.options[mainSelect.selectedIndex];
        let parentId = mainSelect.value;
        let categoryName = selectedOption ? (selectedOption.getAttribute('data-name') || '') : '';

        let subL1Container = document.getElementById('sub_level1_container');
        let subL1Select = document.getElementById('sub_level1_select');
        let subL1Label = document.getElementById('sub_level1_label');
        
        let subL2Container = document.getElementById('sub_level2_container');
        let subL2Select = document.getElementById('sub_level2_select');
        let finalCatInput = document.getElementById('final_category_id');

        subL1Select.value = '';
        subL2Select.value = '';
        subL2Container.classList.add('hidden');
        subL2Select.removeAttribute('required');

        if (!parentId) {
            subL1Container.classList.add('hidden');
            subL1Select.removeAttribute('required');
            finalCatInput.value = '';
            return;
        }

        // PENYESUAIAN UNTUK HANDPHONE & PROVIDER
        if (categoryName.includes('pulsa') || categoryName.includes('paket') || categoryName.includes('voucher') || categoryName.includes('perdana')) {
            subL1Label.innerHTML = 'Pilih Provider <span class="text-rose-500">*</span>';
        } else if (categoryName.includes('handphone') || categoryName.includes('hp')) {
            subL1Label.innerHTML = 'Kondisi / Jenis HP <span class="text-rose-500">*</span>';
        } else {
            subL1Label.innerHTML = 'Sub-Kategori / Jenis <span class="text-rose-500">*</span>';
        }

        let l1Options = subL1Select.querySelectorAll('.sub-l1-option');
        let hasChild = false;

        l1Options.forEach(opt => {
            if (String(opt.getAttribute('data-parent')) === String(parentId)) {
                opt.classList.remove('hidden');
                hasChild = true;
            } else {
                opt.classList.add('hidden');
            }
        });

        if (hasChild) {
            subL1Container.classList.remove('hidden');
            subL1Select.setAttribute('required', 'required');
            finalCatInput.value = '';
        } else {
            subL1Container.classList.add('hidden');
            subL1Select.removeAttribute('required');
            finalCatInput.value = parentId;
        }
    }

    function handleSubLevel1Change() {
        let subL1Select = document.getElementById('sub_level1_select');
        let parentId = subL1Select.value;

        let subL2Container = document.getElementById('sub_level2_container');
        let subL2Select = document.getElementById('sub_level2_select');
        let finalCatInput = document.getElementById('final_category_id');

        subL2Select.value = '';
        finalCatInput.value = '';

        if (parentId) {
            let l2Options = subL2Select.querySelectorAll('.sub-l2-option');
            let hasChild = false;

            l2Options.forEach(opt => {
                if (String(opt.getAttribute('data-parent')) === String(parentId)) {
                    opt.classList.remove('hidden');
                    hasChild = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            if (hasChild) {
                subL2Container.classList.remove('hidden');
                subL2Select.setAttribute('required', 'required');
            } else {
                subL2Container.classList.add('hidden');
                subL2Select.removeAttribute('required');
                finalCatInput.value = parentId;
            }
        } else {
            subL2Container.classList.add('hidden');
            subL2Select.removeAttribute('required');
        }
    }

    function handleSubLevel2Change() {
        let subL2Select = document.getElementById('sub_level2_select');
        let finalCatInput = document.getElementById('final_category_id');
        finalCatInput.value = subL2Select.value;
    }

    function toggleStockFields() {
        let type = document.getElementById('product_type').value;
        let stockFields = document.getElementById('stock_fields');
        if (type === 'physical') {
            stockFields.classList.remove('hidden');
            stockFields.classList.add('grid');
        } else {
            stockFields.classList.add('hidden');
            stockFields.classList.remove('grid');
        }
    }

    function formatProductCurrency(input, targetHiddenId) {
        let rawValue = input.value.replace(/\D/g, '');
        document.getElementById(targetHiddenId).value = rawValue;

        if (rawValue === '') {
            input.value = '';
            return;
        }

        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }

    function validateEditProductForm(e) {
        let finalCatInput = document.getElementById('final_category_id');

        if (!finalCatInput.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Kategori Belum Lengkap',
                text: 'Silakan tentukan sub-kategori/provider secara spesifik sebelum menyimpan.',
                customClass: { popup: 'rounded-2xl font-sans' }
            });
        }
    }
</script>
@endpushz