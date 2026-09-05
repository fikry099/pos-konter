<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
    <!-- HEADER CARD UNGU INDIGO -->
    <div class="bg-indigo-600 px-4 py-3 sm:px-5 sm:py-3.5 text-white flex items-center justify-between">
        <h2 class="font-black text-xs sm:text-sm flex items-center">
            <span class="w-8 h-8 rounded-xl bg-indigo-500/60 flex items-center justify-center mr-2.5 border border-indigo-400/40 shrink-0">
                <i class="fa-solid fa-power-off text-xs sm:text-sm"></i>
            </span>
            <span>Form Penutupan Shift (Closing)</span>
        </h2>
        
        <div class="flex items-center space-x-2">
            <!-- TOMBOL UNTUK MEMBUKA MODAL ABSEN SUSULAN -->
            <button type="button" onclick="openJoinShiftModal()" class="bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white text-[11px] sm:text-xs font-black px-3 py-1.5 rounded-xl transition shadow-md flex items-center space-x-1.5 cursor-pointer">
                <i class="fa-solid fa-user-plus text-[10px]"></i>
                <span>+ Absen Susulan</span>
            </button>

            <span class="text-[10px] sm:text-xs bg-indigo-500/50 text-white font-mono px-2.5 py-1.5 rounded-xl font-bold border border-indigo-400/30 shrink-0">
                Shift #{{ $activeShift->id }}
            </span>
        </div>
    </div>
    
    <div class="p-4 sm:p-5">
        <!-- GRID 2 KOLOM (SISI KIRI: INFO SHIFT, SISI KANAN: FORM INPUT & BUTTON) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-start">
            
            <!-- SISI KIRI: INFORMASI SHIFT AKTIF -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-3">
                <!-- KASIR BERTUGAS -->
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Kasir Bertugas</span>
                    </div>
                    <span class="text-xs sm:text-sm font-black text-slate-800">
                        {{ preg_replace('/\s*\([^)]*\)/', '', $activeShift->staff_names ?? ($activeShift->user->name ?? 'Kasir')) }}
                    </span>
                </div>

                <!-- JAM MULAI SHIFT -->
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Jam Mulai Shift</span>
                    </div>
                    <span class="text-[11px] sm:text-xs font-mono font-bold text-slate-800">
                        {{ $activeShift->start_time->format('d/m/Y H:i') }} WIB
                    </span>
                </div>

                <!-- MODAL UANG AWAL -->
                <div class="flex items-center justify-between pt-0.5">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Modal Uang Awal</span>
                    </div>
                    <span class="text-xs sm:text-sm font-mono font-black text-indigo-700">
                        Rp {{ number_format($activeShift->cash_initial, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- SISI KANAN: FORM INPUT & TOMBOL CLOSING -->
            <form id="close-shift-form" action="{{ route('shifts.close', $activeShift->id) }}" method="POST" class="space-y-3.5">
                @csrf
                
                <!-- Hidden Input Nilai Angka Murni -->
                <input type="hidden" name="cash_actual" id="raw_close_cash_actual" value="0">

                <div>
                    <label class="block text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Total Uang Fisik Asli di Laci (Rp)</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-xs sm:text-sm font-black">
                            Rp
                        </div>
                        <input type="text" id="formatted_close_cash_actual" oninput="formatShiftCloseCurrency(this, 'raw_close_cash_actual')" required placeholder="Hitung seluruh uang kertas & koin..." autocomplete="off" class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 text-slate-900 border rounded-xl border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition text-xs sm:text-sm font-black placeholder-slate-400">
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 mt-1 block font-medium">* Hitung secara teliti fisik uang di laci kasir saat ini sebelum menutup shift.</span>
                </div>

                <button type="button" onclick="confirmCloseShift({{ $expectedCash ?? 0 }})" class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold py-2.5 sm:py-3 rounded-xl text-xs sm:text-sm transition shadow-md shadow-indigo-200 flex items-center justify-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-circle-check text-xs sm:text-sm"></i>
                    <span>Selesaikan Pembukuan & Tutup Shift</span>
                </button>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // FORMAT MASKER RIBUAN UNTUK PENUTUPAN SHIFT
    function formatShiftCloseCurrency(input, targetHiddenId) {
        let rawValue = input.value.replace(/\D/g, '');
        document.getElementById(targetHiddenId).value = rawValue;

        if (rawValue === '') {
            input.value = '';
            return;
        }

        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }

    // KONFIRMASI PENUTUPAN SHIFT DENGAN VALIDASI BLIND CLOSING
    function confirmCloseShift(expectedCash) {
        let rawCash = document.getElementById('raw_close_cash_actual').value;

        if (!rawCash || parseInt(rawCash) < 0 || document.getElementById('formatted_close_cash_actual').value === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Harap masukkan jumlah total uang fisik yang ada di laci kasir terlebih dahulu.',
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });
            return;
        }

        let inputVal = parseFloat(rawCash);
        let diff = inputVal - expectedCash;
        let formatExpected = new Intl.NumberFormat('id-ID').format(expectedCash);
        let formatInput = new Intl.NumberFormat('id-ID').format(inputVal);
        let formatDiff = new Intl.NumberFormat('id-ID').format(Math.abs(diff));

        // 1. KONDISI KAS PAS
        if (diff === 0) {
            Swal.fire({
                title: 'Kas Sesuai (Pas)',
                html: `<p class="text-xs text-slate-600">Uang fisik laci <b>Rp ${formatInput}</b> sesuai dengan pencatatan sistem.</p><p class="text-xs font-bold text-slate-800 mt-2">Selesaikan pembukuan dan tutup shift sekarang?</p>`,
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tutup Shift',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
                    cancelButton: 'rounded-xl font-semibold px-4 py-2 text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('close-shift-form').submit();
                }
            });
        } 
        // 2. KONDISI KAS MINUS
        else if (diff < 0) {
            Swal.fire({
                title: 'Peringatan: Kas Minus!',
                html: `<div class="text-left text-xs space-y-1.5 bg-rose-50 p-3 rounded-xl border border-rose-200/80 my-2">
                        <p class="text-slate-700"><b>Ekspektasi Sistem:</b> Rp ${formatExpected}</p>
                        <p class="text-slate-700"><b>Input Fisik Laci:</b> Rp ${formatInput}</p>
                        <p class="text-rose-600 font-bold"><b>Selisih Kurang:</b> -Rp ${formatDiff}</p>
                       </div>
                       <p class="text-[11px] text-slate-500">Periksa kembali apakah ada kembalian yang salah atau uang tersembunyi di laci.</p>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Tetap Tutup (Minus)',
                cancelButtonText: 'Hitung Ulang Laci',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
                    cancelButton: 'rounded-xl font-semibold px-4 py-2 text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('close-shift-form').submit();
                }
            });
        } 
        // 3. KONDISI KAS SURPLUS (PLUS)
        else {
            Swal.fire({
                title: 'Perhatian: Ada Kelebihan Kas!',
                html: `<div class="text-left text-xs space-y-1.5 bg-amber-50 p-3 rounded-xl border border-amber-200/80 my-2">
                        <p class="text-slate-700"><b>Ekspektasi Sistem:</b> Rp ${formatExpected}</p>
                        <p class="text-slate-700"><b>Input Fisik Laci:</b> Rp ${formatInput}</p>
                        <p class="text-amber-600 font-bold"><b>Selisih Lebih:</b> +Rp ${formatDiff}</p>
                       </div>
                       <p class="text-[11px] text-slate-500">Pastikan semua nota/transaksi barang sudah selesai di-input ke sistem.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Tetap Tutup (Surplus)',
                cancelButtonText: 'Cek Transaksi',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
                    cancelButton: 'rounded-xl font-semibold px-4 py-2 text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('close-shift-form').submit();
                }
            });
        }
    }
</script>
@endpush