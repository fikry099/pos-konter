<!-- MODAL ADD TO CART (POSISI DITENGAH KANVAS) -->
<div id="cartModal" class="fixed inset-0 z-[9999] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
        
        <!-- HEADER MODAL -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-cart-plus"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-base" id="modal_product_name">Nama Produk</h3>
                    <p class="text-xs text-gray-400">Masukkan detail transaksi pembeli (Opsional)</p>
                </div>
            </div>
            <button onclick="closeAddToCartModal()" type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- FORM ADD TO CART -->
        <form action="{{ route('pos.cart.add') }}" method="POST" autocomplete="off" onsubmit="prepareCleanNumbers(event)" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="product_id" id="modal_product_id">
            <input type="hidden" name="is_custom_amount" id="modal_is_custom_amount" value="0">

            <!-- 1. FIELD NOMINAL CUSTOM (TOP-UP / TRANSFER BEBAS) -->
            <div id="custom_amount_container" class="hidden space-y-3 p-3.5 bg-indigo-50/70 rounded-2xl border border-indigo-100">
                <div class="space-y-1.5">
                    <label for="modal_custom_price" class="block text-xs font-bold text-indigo-900">
                        <i class="fa-solid fa-money-bill-wave text-indigo-600 mr-1"></i> Nominal Transfer / Top-Up (Rp):
                    </label>
                    <input type="text" id="modal_custom_price" name="custom_price" placeholder="Contoh: 137.500" oninput="formatRupiahInput(this)" autocomplete="off" class="w-full px-4 py-2.5 text-sm bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-indigo-700 transition">
                </div>
                <div class="space-y-1.5">
                    <label for="modal_admin_fee" class="block text-xs font-bold text-indigo-900">
                        <i class="fa-solid fa-receipt text-indigo-600 mr-1"></i> Biaya Admin / Jasa (Rp):
                    </label>
                    <input type="text" id="modal_admin_fee" name="admin_fee" value="2.500" placeholder="Contoh: 2.500" oninput="formatRupiahInput(this)" autocomplete="off" class="w-full px-4 py-2.5 text-sm bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-700 transition">
                </div>
            </div>

            <!-- 2. FIELD NO HP / ID PELANGGAN -->
            <div id="phone_field_container" class="space-y-1.5">
                <label for="modal_target_phone" class="block text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-phone text-indigo-500 mr-1"></i> Nomor HP / ID Pelanggan <span class="text-gray-400 font-normal">(Opsional)</span>:
                </label>
                <input type="text" id="modal_target_phone" name="target_number" placeholder="Contoh: 081234567890 / ID PLN (Bisa Kosong)" autocomplete="off" aria-autocomplete="none" class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white font-medium transition">
            </div>

            <!-- 3. FIELD TRANSFER BANK -->
            <div id="bank_field_container" class="hidden space-y-3">
                <div class="space-y-1.5">
                    <label for="modal_account_number" class="block text-xs font-bold text-gray-700">
                        <i class="fa-solid fa-credit-card text-teal-600 mr-1"></i> Nomor Rekening Tujuan <span class="text-gray-400 font-normal">(Opsional)</span>:
                    </label>
                    <input type="text" id="modal_account_number" name="account_number" placeholder="Contoh: 1234567890 (Bisa Kosong)" autocomplete="off" class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white font-medium transition">
                </div>
                <div class="space-y-1.5">
                    <label for="modal_account_name" class="block text-xs font-bold text-gray-700">
                        <i class="fa-solid fa-user text-teal-600 mr-1"></i> Nama Pemilik Rekening <span class="text-gray-400 font-normal">(Opsional)</span>:
                    </label>
                    <input type="text" id="modal_account_name" name="account_name" placeholder="Contoh: Budi Santoso (Bisa Kosong)" autocomplete="off" class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white font-medium transition">
                </div>
            </div>

            <!-- 4. FIELD QUANTITY -->
            <div id="qty_field_container" class="hidden space-y-1.5">
                <label for="modal_quantity" class="block text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-layer-group text-amber-500 mr-1"></i> Jumlah / Qty:
                </label>
                <input type="number" id="modal_quantity" name="quantity" min="1" value="1" autocomplete="off" class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white font-bold text-center transition">
            </div>

            <!-- BUTTONS ACTION -->
            <div class="flex items-center space-x-3 pt-3">
                <button type="button" onclick="closeAddToCartModal()" class="w-1/2 py-3 rounded-xl border border-slate-200 text-gray-600 font-bold text-xs hover:bg-slate-50 active:scale-95 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-200 active:scale-95 transition flex items-center justify-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Masukkan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT PEMFORMATAN RUPIAH & PEMBERSIH ATRIBUT REQUIRED -->
<script>
    // 1. Fungsi Menghapus Atribut Required Secara Paksa
    function clearModalRequiredFields() {
        const modalInputs = document.querySelectorAll('#cartModal input');
        modalInputs.forEach(input => {
            input.removeAttribute('required');
            input.required = false;
        });
    }

    // Monitor perubahan modal jika JS eksternal mencoba menyisipkan required
    document.addEventListener('DOMContentLoaded', function() {
        const cartModal = document.getElementById('cartModal');
        if (cartModal) {
            const observer = new MutationObserver(function() {
                clearModalRequiredFields();
            });

            observer.observe(cartModal, { 
                attributes: true, 
                subtree: true, 
                childList: true 
            });
        }
    });

    // 2. Fungsi Format Angka ke Rupiah (Titik Ribuan)
    function formatRupiahInput(element) {
        let value = element.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        element.value = rupiah;
    }

    // 3. Pembersih Titik & Required Sebelum Form Di-Submit
    function prepareCleanNumbers(e) {
        clearModalRequiredFields();

        let customPriceInput = document.getElementById('modal_custom_price');
        let adminFeeInput = document.getElementById('modal_admin_fee');

        if (customPriceInput && customPriceInput.value) {
            customPriceInput.value = customPriceInput.value.replace(/[^0-9]/g, '');
        }

        if (adminFeeInput && adminFeeInput.value) {
            adminFeeInput.value = adminFeeInput.value.replace(/[^0-9]/g, '');
        }
    }
</script>