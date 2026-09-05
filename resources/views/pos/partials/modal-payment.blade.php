<!-- ========================================== -->
<!-- MODAL POPUP PEMBAYARAN (COMPACT NO-SCROLL) -->
<!-- ========================================== -->
<div id="payment_modal" class="fixed inset-0 z-[9999] hidden bg-slate-900/60 backdrop-blur-md flex items-center justify-start pl-3 sm:pl-8 lg:pl-12 pr-3 lg:pr-[420px] p-2 sm:p-4 transition-all duration-300">
    <div class="bg-white w-full max-w-sm sm:max-w-md rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 my-auto mx-auto lg:mx-0" id="payment_modal_card">
        
        <!-- HEADER MODAL -->
        <div class="px-4 pt-3.5 pb-2 bg-white flex items-center justify-between rounded-t-2xl border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-sm">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm leading-tight">Pilih Pembayaran</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Selesaikan transaksi kasir</p>
                </div>
            </div>
            <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1 rounded-lg transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-3.5 sm:p-4 space-y-3">
            
            <!-- RINGKASAN TOTAL TAGIHAN -->
            <div class="bg-gradient-to-r from-indigo-50/80 via-purple-50/40 to-slate-50 border border-indigo-100/80 p-2.5 sm:p-3 rounded-xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[9px] font-bold text-indigo-900/70 uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-lg sm:text-xl font-black text-indigo-700 leading-tight" id="modal_total_display">Rp 0</span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-white border border-indigo-100 flex items-center justify-center text-indigo-600 text-xs sm:text-sm shadow-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <!-- TAB METODE PEMBAYARAN -->
            <div class="grid grid-cols-2 gap-1.5 bg-slate-100/80 p-1 rounded-xl border border-slate-200/60">
                <button type="button" id="btn_select_cash" onclick="switchModalPayment('cash')" class="py-1.5 px-2 rounded-lg font-extrabold text-[11px] flex items-center justify-center space-x-1.5 transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-money-bill-wave text-[10px]"></i>
                    <span>CASH / TUNAI</span>
                </button>
                <button type="button" id="btn_select_qris" onclick="switchModalPayment('qris')" class="py-1.5 px-2 rounded-lg font-extrabold text-[11px] flex items-center justify-center space-x-1.5 transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-qrcode text-[10px]"></i>
                    <span>QRIS</span>
                </button>
            </div>

            <!-- PANEL 1: TUNAI / CASH -->
            <div id="modal_cash_panel" class="space-y-2.5">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-[11px] font-bold text-slate-700">Uang Tunai Pembeli</label>
                        <button type="button" onclick="setModalQuickPay(currentCartTotal)" class="text-[9px] font-extrabold text-indigo-600 hover:text-indigo-800 transition cursor-pointer">
                            [ Uang Pas ]
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-black">Rp</span>
                        <input type="text" id="modal_pay_input" oninput="formatModalCurrency(this)" autocomplete="off" placeholder="0" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-black text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    </div>
                </div>

                <!-- PECAHAN UANG CEPAT -->
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button" onclick="setModalQuickPay(10000)" class="bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 py-1 rounded-lg text-[11px] font-extrabold transition active:scale-95 cursor-pointer">10k</button>
                    <button type="button" onclick="setModalQuickPay(20000)" class="bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 py-1 rounded-lg text-[11px] font-extrabold transition active:scale-95 cursor-pointer">20k</button>
                    <button type="button" onclick="setModalQuickPay(50000)" class="bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 py-1 rounded-lg text-[11px] font-extrabold transition active:scale-95 cursor-pointer">50k</button>
                    <button type="button" onclick="setModalQuickPay(100000)" class="bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 py-1 rounded-lg text-[11px] font-extrabold transition active:scale-95 cursor-pointer">100k</button>
                </div>

                <!-- DISPLAY KEMBALIAN -->
                <div class="bg-emerald-50/80 border-2 border-emerald-200/80 p-2.5 sm:p-3 rounded-xl flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-[9px] font-black text-emerald-700/80 uppercase tracking-wider block">Kembalian</span>
                        <span id="modal_change_display" class="font-black text-emerald-600 text-lg sm:text-xl leading-none">Rp 0</span>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xs sm:text-sm shrink-0">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
            </div>

            <!-- PANEL 2: QRIS (WEBCAM BUKTI - COMPACT) -->
            <div id="modal_qris_panel" class="hidden space-y-2 text-center">
                <span class="text-[11px] font-bold text-slate-700 block">Ambil Foto Bukti Transfer QRIS</span>
                
                <div id="qris_camera" class="w-full h-28 sm:h-32 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-center overflow-hidden mx-auto shadow-inner">
                    <span class="text-[10px] text-slate-400 font-medium">Menyiapkan Kamera...</span>
                </div>
                <div id="qris_result" class="hidden w-full h-28 sm:h-32 rounded-xl border overflow-hidden mx-auto shadow-sm"></div>

                <div class="flex justify-center space-x-2 pt-0.5">
                    <button type="button" onclick="take_qris_snapshot()" id="btn_snap_qris" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] px-3 py-1.5 rounded-xl font-bold transition shadow-md shadow-indigo-200 cursor-pointer">
                        <i class="fa-solid fa-camera mr-1 text-[10px]"></i> Ambil Foto Bukti
                    </button>
                    <button type="button" onclick="reset_qris_camera()" id="btn_reset_qris" class="hidden bg-rose-600 hover:bg-rose-700 text-white text-[11px] px-3 py-1.5 rounded-xl font-bold transition shadow-md cursor-pointer">
                        <i class="fa-solid fa-rotate-left mr-1 text-[10px]"></i> Foto Ulang
                    </button>
                </div>
            </div>

            <!-- TOMBOL KONFIRMASI PEMBAYARAN UTAMA -->
            <button type="button" onclick="processFinalCheckout()" class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold py-2.5 rounded-xl text-xs transition shadow-md shadow-indigo-200 flex items-center justify-center space-x-2 cursor-pointer mt-1">
                <i class="fa-solid fa-circle-check text-xs"></i>
                <span>Konfirmasi Pembayaran</span>
            </button>

        </div>
    </div>
</div>