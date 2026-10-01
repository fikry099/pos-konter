<div id="detail_shift_modal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden transition-all duration-300">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl border border-slate-100 space-y-3.5 transform transition-all max-h-[90vh] overflow-y-auto no-scrollbar">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <h3 class="text-xs sm:text-sm font-black text-slate-800 flex items-center">
                <i class="fa-solid fa-file-invoice text-indigo-600 mr-2"></i> Detail Audit Shift Kasir
            </h3>
            <button type="button" onclick="closeShiftDetail()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-3">
            <!-- FOTO BUKTI ABSENSI -->
            <div>
                <p class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider mb-1.5">Foto Bukti Buka Shift / Absensi</p>
                <div id="modal_photo_wrapper" class="w-full h-40 sm:h-48 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center relative">
                    <img id="modal_photo_img" src="" alt="Foto Absensi Kasir" class="w-full h-full object-cover hidden">
                    <div id="modal_photo_empty" class="text-center p-3 text-slate-400">
                        <i class="fa-solid fa-image text-2xl mb-1 text-slate-300"></i>
                        <p class="text-[10px] font-bold text-slate-400">Tidak ada lampiran foto absensi</p>
                    </div>
                </div>
            </div>

            <!-- RINCIAN DATA SHIFT -->
            <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Nama Kasir</p>
                    <p id="modal_kasir_name" class="font-extrabold text-slate-800 text-xs mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Waktu Buka</p>
                    <p id="modal_start_date" class="font-bold text-slate-700 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Waktu Tutup</p>
                    <p id="modal_close_date" class="font-bold text-slate-700 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Modal Kas Awal</p>
                    <p id="modal_start_cash" class="font-mono font-extrabold text-slate-800 mt-0.5 truncate">-</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 text-xs">
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Penjualan Tunai</p>
                    <p id="modal_cash_sales" class="font-mono font-extrabold text-indigo-700 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Pengeluaran Kas</p>
                    <p id="modal_expenses" class="font-mono font-extrabold text-rose-600 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Estimasi Harus Ada</p>
                    <p id="modal_expected" class="font-mono font-extrabold text-slate-800 mt-0.5 truncate">-</p>
                </div>
                <div>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase">Fisik Uang Kasir</p>
                    <p id="modal_actual" class="font-mono font-extrabold text-slate-800 mt-0.5 truncate">-</p>
                </div>
            </div>

            <!-- SELISIH KAS -->
            <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between bg-slate-50 text-xs">
                <span class="font-extrabold text-slate-700 uppercase text-[10px]">Selisih Kasir:</span>
                <span id="modal_difference" class="font-mono text-xs sm:text-sm font-black">-</span>
            </div>
        </div>

        <button type="button" onclick="closeShiftDetail()" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-2.5 rounded-xl text-xs transition cursor-pointer">
            Tutup Detail
        </button>
    </div>
</div>