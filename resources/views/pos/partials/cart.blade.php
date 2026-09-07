@php 
    $totalPrice = array_sum(array_column($cart, 'subtotal')); 
    $totalItems = count($cart);
@endphp

<!-- FLOATING CART BUTTON -->
<div class="fixed bottom-20 right-4 sm:bottom-24 sm:right-6 z-[9998] flex flex-col items-end pointer-events-none">
    <button type="button" onclick="toggleCartDrawer()" class="pointer-events-auto bg-indigo-600 hover:bg-indigo-700 active:scale-90 text-white w-12 h-12 sm:w-14 sm:h-14 rounded-full shadow-2xl shadow-indigo-600/40 flex items-center justify-center text-lg sm:text-xl transition-all duration-300 border-2 border-white cursor-pointer group relative">
        <i class="fa-solid fa-cart-shopping group-hover:scale-110 transition-transform"></i>
        
        @if($totalItems > 0)
            <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-md animate-bounce">
                {{ $totalItems > 99 ? '99+' : $totalItems }}
            </span>
        @endif
    </button>
</div>

<!-- BACKDROP OVERLAY KERANJANG -->
<div id="cart_drawer_backdrop" onclick="toggleCartDrawer()" class="fixed inset-0 z-50 bg-slate-900/50 hidden transition-opacity duration-300"></div>

<!-- SLIDE-OVER DRAWER KERANJANG BELANJA -->
<div id="cart_drawer" class="fixed inset-y-0 right-0 z-[9999] w-full max-w-md h-screen bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <!-- HEADER KERANJANG -->
    <div class="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-black">Keranjang Belanja</h3>
                <p class="text-[10px] text-slate-400 font-medium">Item siap ditransaksikan</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if(!empty($cart))
                <button type="button" onclick="confirmClearCart()" class="text-[11px] text-rose-400 hover:text-rose-300 font-bold bg-rose-950/50 px-2.5 py-1 rounded-lg border border-rose-800/50 transition cursor-pointer flex items-center space-x-1">
                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                    <span>Kosongkan</span>
                </button>
            @endif
            <button type="button" onclick="toggleCartDrawer()" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    </div>

    <!-- LIST ITEM KERANJANG -->
    <div class="flex-1 overflow-y-auto p-3.5 space-y-2.5">
        @forelse($cart as $key => $item)
            @php
                $productId = $item['product_id'] ?? $item['id'] ?? $key;
                $cartProduct = \App\Models\Product::with('category.parent.parent')->find($productId);
                
                $catName = strtolower($item['category_name'] ?? $cartProduct?->category?->name ?? '');
                $catSlug = strtolower($item['category_slug'] ?? $cartProduct?->category?->slug ?? '');
                
                $parentCat = $cartProduct?->category?->parent;
                $parentName = strtolower($parentCat?->name ?? '');
                $parentSlug = strtolower($parentCat?->slug ?? '');

                $grandParentCat = $parentCat?->parent;
                $grandParentName = strtolower($grandParentCat?->name ?? '');
                $grandParentSlug = strtolower($grandParentCat?->slug ?? '');

                $rawName = $item['name'] ?? $cartProduct?->name ?? '';
                $itemName = strtolower($rawName);
                $itemType = strtolower($item['type'] ?? $cartProduct?->type ?? 'physical');

                $allCatString = $catName . ' ' . $catSlug . ' ' . $parentName . ' ' . $parentSlug . ' ' . $grandParentName . ' ' . $grandParentSlug;

                $isVoucherOrPerdana = str_contains($allCatString, 'voucher') || str_contains($allCatString, 'perdana') || str_contains($itemName, 'voucher') || str_contains($itemName, 'perdana');

                $isAccessoryKeyword = str_contains($allCatString, 'aksesoris') || 
                                       str_contains($allCatString, 'accessory') || 
                                       str_contains($allCatString, 'proteksi') || 
                                       str_contains($allCatString, 'power') || 
                                       str_contains($allCatString, 'audio') || 
                                       str_contains($allCatString, 'penyimpanan') || 
                                       str_contains($allCatString, 'mount') ||
                                       str_contains($itemName, 'case') ||
                                       str_contains($itemName, 'casing') ||
                                       str_contains($itemName, 'charger') ||
                                       str_contains($itemName, 'headset');

                $isAccessory = ($itemType === 'physical' && !$isVoucherOrPerdana) || $isAccessoryKeyword;

                // =========================================================
                // GUNAKAN NAMA UTUH LANGSUNG DARI SESSION/CONTROLLER
                // =========================================================
                $cartDisplayName = $rawName;
            @endphp

            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/80 space-y-2 shadow-sm hover:border-indigo-200 transition">
                <div class="flex items-start justify-between space-x-2.5">
                    <div class="flex-1 min-w-0">
                        <h5 class="text-xs font-extrabold text-slate-800 truncate">{{ $cartDisplayName }}</h5>
                        
                        @if(!empty($item['target_phone']))
                            <span class="text-[10px] font-mono bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-md font-bold inline-block mt-0.5">
                                <i class="fa-solid fa-mobile-screen mr-1"></i>{{ $item['target_phone'] }}
                            </span>
                        @endif

                        <div class="text-xs text-indigo-700 font-black font-mono mt-0.5">
                            Rp {{ number_format($item['selling_price'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="flex items-center space-x-1.5 shrink-0">
                        <form action="{{ route('pos.cart.update', $key) }}" method="POST" class="flex items-center">
                            @csrf
                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" onchange="this.form.submit()" class="w-11 text-center text-xs bg-white text-slate-800 border border-slate-300 rounded-lg py-1 font-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </form>
                        <button type="button" onclick="confirmRemoveCart('{{ route('pos.cart.remove', $key) }}', '{{ addslashes($cartDisplayName) }}')" class="text-slate-400 hover:text-rose-500 p-1 rounded-lg hover:bg-rose-50 transition cursor-pointer">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- 1. SELEKSI PENJUAL (AKSESORIS) -->
                @if($isAccessory)
                    <div class="pt-2 border-t border-slate-200/60 space-y-1.5">
                        @php $staffList = $shiftStaffs ?? collect([]); @endphp

                        <label class="text-[11px] font-extrabold text-amber-800 uppercase flex items-center">
                            <i class="fa-solid fa-user-tag text-amber-600 mr-1.5"></i> Penjual Aksesoris:
                        </label>

                        @if($staffList->count() > 1)
                            @php
                                $selectedStaffId = $item['served_by_user_id'] ?? '';
                                $selectedStaff = $staffList->firstWhere('id', $selectedStaffId);
                                $staffDisplayName = $selectedStaff ? $selectedStaff->name : 'Pilih Karyawan Penjual';
                            @endphp

                            <button type="button" 
                                    onclick="openSelectModal('seller_modal_{{ $key }}')"
                                    class="w-full text-xs font-black bg-amber-50 border-2 border-amber-300 hover:border-amber-400 text-amber-950 rounded-2xl px-4 py-3 flex items-center justify-between cursor-pointer active:scale-98 transition-all shadow-xs">
                                <span class="flex items-center space-x-2 truncate">
                                    <span>👤</span>
                                    <span id="seller_display_{{ $key }}" class="truncate">{{ $staffDisplayName }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-amber-700 text-xs shrink-0 ml-2"></i>
                            </button>

                            @push('cart_modals')
                                <div id="seller_modal_{{ $key }}" class="fixed inset-0 z-[100000] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                    <div class="absolute inset-0" onclick="closeSelectModal('seller_modal_{{ $key }}')"></div>
                                    <div class="bg-white rounded-3xl w-full max-w-sm p-5 space-y-3 shadow-2xl border border-amber-100 relative z-10">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                            <h4 class="font-black text-slate-800 text-sm flex items-center">
                                                <i class="fa-solid fa-user-tag text-amber-600 mr-2"></i> Pilih Karyawan Penjual
                                            </h4>
                                            <button type="button" onclick="closeSelectModal('seller_modal_{{ $key }}')" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                                <i class="fa-solid fa-xmark text-base"></i>
                                            </button>
                                        </div>

                                        <div class="space-y-2 max-h-60 overflow-y-auto pt-1">
                                            @foreach($staffList as $staff)
                                                <button type="button" 
                                                        onclick="selectSellerAndSubmit('{{ route('pos.cart.assign_staff', $key) }}', '{{ $staff->id }}', '{{ addslashes($staff->name) }}', 'seller_modal_{{ $key }}', '{{ $key }}')"
                                                        class="seller-option-btn-{{ $key }} w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95
                                                        {{ $selectedStaffId == $staff->id ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}"
                                                        data-staff-id="{{ $staff->id }}">
                                                    <span>👤 {{ $staff->name }}</span>
                                                    <i class="fa-solid fa-check text-xs {{ $selectedStaffId == $staff->id ? '' : 'hidden' }}"></i>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endpush
                        @else
                            <div class="text-xs font-black text-emerald-800 bg-emerald-50 border-2 border-emerald-200 rounded-2xl px-4 py-3 flex items-center justify-between">
                                <span class="flex items-center">
                                    <i class="fa-solid fa-user-check text-emerald-600 mr-2"></i>
                                    Penjual: {{ $staffList->first()->name ?? auth()->user()->name }}
                                </span>
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- 2. SELEKSI SERVER PPOB ASLI (PROPANA, DIGIPOS, TOPINDO, DLL) -->
                @if($itemType === 'digital')
                    @php
                        $isEwalletOrBank = str_contains($itemName, 'transfer') || str_contains($itemName, 'top-up') || str_contains($itemName, 'dana') || str_contains($itemName, 'gopay') || str_contains($itemName, 'ovo') || str_contains($itemName, 'shopee') || str_contains($itemName, 'bca') || str_contains($itemName, 'bank');
                        
                        // SERVER PPOB MURNI (Sesuai Bawaan Asli)
                        $providers = $isEwalletOrBank 
                            ? ['Propana' => '💳 Propana', 'Mitra Shopee' => '💳 Mitra Shopee', 'Seabank' => '💳 Seabank']
                            : ['Digipos' => '📱 Digipos (Telkomsel)', 'Rita' => '📱 Rita (Three)', 'Dompul' => '📱 Dompul (XL/Axis)', 'Simpel' => '📱 Simpel (indosat)', 'Propana' => '📱 Propana (Smartfren)'];

                        // JIKA 'digital_provider' DI SESSION BUKAN KUNCI SERVER YANG VALID, DEFAULTKAN KE 'Propana' ATAU 'Digipos'
                        $savedProvider = $item['digital_provider'] ?? '';
                        if (array_key_exists($savedProvider, $providers)) {
                            $currentProvider = $savedProvider;
                        } else {
                            $currentProvider = $isEwalletOrBank ? 'Propana' : 'Digipos';
                        }
                    @endphp

                    <div class="pt-2 border-t border-slate-200/60 space-y-1.5">
                        <label class="text-[11px] font-extrabold text-indigo-900 uppercase flex items-center">
                            <i class="fa-solid fa-server text-indigo-600 mr-1.5"></i> Server / Provider:
                        </label>

                        <!-- TOMBOL PEMICU MODAL PILIH SERVER -->
                        <button type="button" 
                                onclick="openSelectModal('server_modal_{{ $key }}')"
                                class="w-full text-xs font-black bg-indigo-50 border-2 border-indigo-200 hover:border-indigo-400 text-indigo-950 rounded-2xl px-4 py-3 flex items-center justify-between cursor-pointer active:scale-98 transition-all shadow-xs">
                            <span id="provider_display_{{ $key }}" class="truncate">{{ $providers[$currentProvider] ?? $currentProvider }}</span>
                            <i class="fa-solid fa-chevron-down text-indigo-600 text-xs shrink-0 ml-2"></i>
                        </button>

                        @push('cart_modals')
                            <!-- MODAL SERVER OPTIONS -->
                            <div id="server_modal_{{ $key }}" class="fixed inset-0 z-[100000] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                <div class="absolute inset-0" onclick="closeSelectModal('server_modal_{{ $key }}')"></div>
                                <div class="bg-white rounded-3xl w-full max-w-sm p-5 space-y-3 shadow-2xl border border-indigo-100 relative z-10">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                        <h4 class="font-black text-slate-800 text-sm flex items-center">
                                            <i class="fa-solid fa-server text-indigo-600 mr-2"></i> Pilih Server PPOB
                                        </h4>
                                        <button type="button" onclick="closeSelectModal('server_modal_{{ $key }}')" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                            <i class="fa-solid fa-xmark text-base"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-2 max-h-72 overflow-y-auto pt-1">
                                        @foreach($providers as $val => $label)
                                            <button type="button" 
                                                    onclick="selectServerAndSubmit('{{ route('pos.cart.update', $key) }}', '{{ $val }}', {{ $item['qty'] }}, 'server_modal_{{ $key }}', '{{ $key }}', '{{ addslashes($label) }}')"
                                                    class="server-option-btn-{{ $key }} w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95
                                                    {{ $currentProvider === $val ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}"
                                                    data-provider-val="{{ $val }}">
                                                <span>{{ $label }}</span>
                                                <i class="fa-solid fa-check text-xs {{ $currentProvider === $val ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endpush
                    </div>
                @endif
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center text-slate-400 py-16 space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-300 text-xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <p class="text-xs font-bold text-slate-500">Keranjang belanja masih kosong</p>
                <p class="text-[10px] text-slate-400">Pilih produk dari katalog untuk menambahkan</p>
            </div>
        @endforelse
    </div>

    <!-- FOOTER & TOMBOL PROSES BAYAR -->
    <div class="p-4 bg-slate-50 border-t border-slate-200 space-y-3 shrink-0">
        <div class="space-y-1">
            <div class="flex items-center justify-between text-xs text-slate-500 font-bold">
                <span>Total Item Belanja</span>
                <span class="font-mono text-slate-800">{{ $totalItems }} Item</span>
            </div>
            <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                <span class="text-xs font-extrabold text-slate-700 uppercase">Total Tagihan</span>
                <span class="text-lg sm:text-xl font-black text-indigo-700 font-mono">
                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <form id="pos_checkout_form" action="{{ route('pos.checkout') }}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" id="form_payment_method" value="cash">
            <input type="hidden" name="pay_amount" id="form_pay_amount" value="0">
            <input type="hidden" name="payment_proof" id="form_payment_proof" value="">

            <button type="button" onclick="openPaymentModal({{ $totalPrice }})" {{ empty($cart) ? 'disabled' : '' }} class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-extrabold py-3 rounded-xl text-xs sm:text-sm transition shadow-md shadow-indigo-200 flex items-center justify-center space-x-2 cursor-pointer active:scale-95">
                <i class="fa-solid fa-cash-register"></i>
                <span>Bayar & Simpan Transaksi</span>
            </button>
        </form>
    </div>
</div>

<!-- CONTAINER UNTUK SELURUH MODAL ITEM KERANJANG -->
@stack('cart_modals')

<!-- INCLUDE PARTIAL MODAL PEMBAYARAN -->
@include('pos.partials.modal-payment')

<!-- URL CONFIGURATION FOR JS -->
<script>
    window.clearCartUrl = "{{ route('pos.cart.clear') }}";
</script>

<!-- INCLUDE EXTERNAL JS CONTROL -->
<script src="{{ asset('js/pos/cart-payment.js') }}"></script>