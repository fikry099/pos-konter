@extends('layouts.app')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    
    <!-- HEADER -->
    <div class="bg-slate-900 text-white p-5 rounded-3xl shadow-xl flex items-center justify-between border border-slate-800">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl font-bold shrink-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h1 class="text-base sm:text-lg font-black tracking-wide">Layanan Tarik Tunai (Cash-Out)</h1>
                <p class="text-xs text-slate-400 font-medium">Pelanggan transfer via m-Banking/QRIS, Kasir berikan uang fisik laci.</p>
            </div>
        </div>
        <a href="{{ route('transactions.index') }}" class="hidden sm:flex items-center space-x-2 bg-slate-800 hover:bg-slate-700 text-slate-300 px-3.5 py-2 rounded-xl text-xs font-bold transition">
            <i class="fa-solid fa-receipt"></i>
            <span>Riwayat Transaksi</span>
        </a>
    </div>

    <!-- FORM INPUT TARIK TUNAI -->
    <form action="{{ route('cash_out.store') }}" method="POST" autocomplete="off" onsubmit="prepareCleanNumbers(event)" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- NOMINAL DITARIK -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-money-bill-wave text-emerald-600 mr-2"></i> Uang Fisik Yang Diserahkan (Rp):
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400 text-lg">Rp</span>
                    <input type="text" id="cash_amount_input" autocomplete="off" required oninput="formatRupiahInput(this); calculateTotalTransfer();" placeholder="0" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 text-slate-900 border-2 border-slate-200 rounded-2xl font-black text-xl focus:border-indigo-600 focus:bg-white focus:outline-none transition">
                    <input type="hidden" name="cash_amount" id="cash_amount_raw">
                </div>
            </div>

            <!-- MASUK KE REKENING (PEMICU MODAL) -->
            <div class="space-y-1.5">
                <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-building-columns text-indigo-600 mr-2"></i> Masuk Ke Rekening / Wallet:
                </label>

                <input type="hidden" name="target_bank" id="target_bank_value" value="BANK BCA" required>

                <!-- TOMBOL DROPDOWN PEMICU MODAL -->
                <button type="button" 
                        onclick="openBankModal()" 
                        class="w-full text-xs font-black bg-indigo-50 border-2 border-indigo-200 hover:border-indigo-400 text-indigo-950 rounded-2xl px-4 py-3.5 flex items-center justify-between cursor-pointer active:scale-98 transition-all shadow-xs">
                    <span class="flex items-center space-x-2 truncate">
                        <i class="fa-solid fa-building-columns text-indigo-600 text-sm"></i>
                        <span id="target_bank_display" class="truncate font-black">Bank BCA Cabang</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-indigo-600 text-xs shrink-0 ml-2"></i>
                </button>
            </div>

            <!-- BIAYA ADMIN -->
            <div class="space-y-1.5">
                <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-coins text-amber-500 mr-2"></i> Biaya Admin (Keuntungan):
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">Rp</span>
                    <input type="text" id="admin_fee_input" value="5.000" autocomplete="off" required oninput="formatRupiahInput(this); calculateTotalTransfer();" class="w-full pl-10 pr-4 py-3.5 bg-slate-50 text-slate-800 border-2 border-slate-200 rounded-2xl font-black text-sm focus:border-indigo-600 focus:outline-none">
                    <input type="hidden" name="admin_fee" id="admin_fee_raw" value="5000">
                </div>
            </div>

            <!-- CATATAN PELANGGAN -->
            <div class="space-y-1.5">
                <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Nomor Rek / HP Pengirim (Opsional):</label>
                <input type="text" name="account_number" autocomplete="off" placeholder="Contoh: 08123456789" class="w-full px-4 py-3 bg-slate-50 text-slate-800 border-2 border-slate-200 rounded-2xl font-bold text-xs focus:border-indigo-600 focus:outline-none">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Nama Pengirim / Pemilik Rek (Opsional):</label>
                <input type="text" name="account_name" autocomplete="off" placeholder="Contoh: Ahmad Subagjo" class="w-full px-4 py-3 bg-slate-50 text-slate-800 border-2 border-slate-200 rounded-2xl font-bold text-xs focus:border-indigo-600 focus:outline-none">
            </div>

        </div>

        <!-- TOTAL YANG HARUS DITRANSFER PELANGGAN -->
        <div class="p-4 bg-indigo-50 border-2 border-indigo-100 rounded-2xl flex items-center justify-between">
            <div>
                <span class="text-[11px] font-black uppercase text-indigo-900 block">Total Transfer Pelanggan</span>
                <span class="text-[10px] text-indigo-600 font-bold">Uang Fisik + Biaya Admin</span>
            </div>
            <span id="total_transfer_display" class="text-xl sm:text-2xl font-black text-indigo-700 font-mono">Rp 5.000</span>
        </div>

        <!-- AMBIL FOTO BUKTI TRANSFER -->
        <div class="space-y-3 pt-2 border-t border-slate-100">
            <label class="text-xs font-extrabold text-slate-800 uppercase flex items-center">
                <i class="fa-solid fa-camera text-indigo-600 mr-2"></i> Foto Bukti Transfer Pelanggan (Wajib):
            </label>

            <input type="hidden" name="payment_proof" id="form_payment_proof" required>

            <div class="bg-slate-900 rounded-3xl p-3 flex flex-col items-center justify-center min-h-[320px] sm:min-h-[360px] relative overflow-hidden border border-slate-800">
                <video id="qris_video" class="w-full h-[300px] sm:h-[340px] object-cover rounded-2xl hidden" autoplay playsinline></video>
                <div id="qris_placeholder" class="text-center text-slate-400 space-y-2 py-8">
                    <i class="fa-solid fa-camera-retro text-4xl text-slate-600"></i>
                    <p class="text-xs font-bold text-slate-400">Kamera belum diaktifkan</p>
                </div>
                <div id="qris_result" class="hidden w-full h-[300px] sm:h-[340px]"></div>
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <button type="button" id="btn_start_cam" onclick="startQrisCamera()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-extrabold py-3.5 rounded-2xl text-xs transition flex items-center justify-center space-x-2 cursor-pointer shadow-sm">
                    <i class="fa-solid fa-power-off"></i>
                    <span>Buka Kamera</span>
                </button>
                <button type="button" id="btn_snap_qris" onclick="take_qris_snapshot()" class="hidden flex-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold py-3.5 rounded-2xl text-xs transition items-center justify-center space-x-2 cursor-pointer shadow-sm">
                    <i class="fa-solid fa-camera"></i>
                    <span>Ambil Foto Bukti</span>
                </button>
                <button type="button" id="btn_reset_qris" onclick="reset_qris_camera()" class="hidden flex-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold py-3.5 rounded-2xl text-xs transition items-center justify-center space-x-2 cursor-pointer shadow-sm">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Foto Ulang</span>
                </button>
            </div>
        </div>

        <!-- SUBMIT -->
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-extrabold py-4 rounded-2xl text-sm transition shadow-lg shadow-indigo-200 flex items-center justify-center space-x-2 cursor-pointer">
            <i class="fa-solid fa-check-circle text-base"></i>
            <span>Proses Tarik Tunai & Diserahkan Uang</span>
        </button>
    </form>
</div>

<!-- MODAL PILIHAN BANK / REKENING DENGAN COVERAGE 100% VIEWPORT -->
<div id="bank_modal" class="fixed -inset-10 z-[999999] hidden backdrop-blur-none flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/80" onclick="closeBankModal()"></div>
    <div class="bg-white rounded-3xl w-full max-w-sm p-5 space-y-3 shadow-2xl border border-indigo-100 relative z-10 my-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-black text-slate-800 text-sm flex items-center">
                <i class="fa-solid fa-building-columns text-indigo-600 mr-2"></i> Pilih Rekening / Wallet
            </h4>
            <button type="button" onclick="closeBankModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-2 max-h-72 overflow-y-auto pt-1">
            @php
                $qrisKey = 'QRIS ' . strtoupper($storeName ?? 'CABANG');
                $qrisLabel = 'QRIS ' . ucwords(strtolower($storeName ?? 'Cabang'));

                $bankModalOptions = [
                    'BANK BCA'     => ['label' => 'Bank BCA Cabang', 'icon' => 'fa-building-columns'],
                    $qrisKey       => ['label' => $qrisLabel, 'icon' => 'fa-qrcode'],
                ];
            @endphp

            @foreach($bankModalOptions as $val => $opt)
                <button type="button" 
                        onclick="selectBankModal('{{ $val }}', '{{ $opt['label'] }}')" 
                        class="bank-modal-item w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95
                        {{ $loop->first ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}"
                        data-val="{{ $val }}">
                    <span class="flex items-center space-x-2.5">
                        <i class="fa-solid {{ $opt['icon'] }} text-sm"></i>
                        <span>{{ $opt['label'] }}</span>
                    </span>
                    <i class="fa-solid fa-check text-xs check-icon {{ $loop->first ? '' : 'hidden' }}"></i>
                </button>
            @endforeach
        </div>
    </div>
</div>

<script>
    // --- 1. LOGIKA MODAL DROPDOWN BANK ---
    function openBankModal() {
        document.getElementById('bank_modal').classList.remove('hidden');
    }

    function closeBankModal() {
        document.getElementById('bank_modal').classList.add('hidden');
    }

    function selectBankModal(val, label) {
        document.getElementById('target_bank_value').value = val;
        document.getElementById('target_bank_display').innerText = label;

        document.querySelectorAll('.bank-modal-item').forEach(btn => {
            let isSelected = btn.getAttribute('data-val') === val;
            let check = btn.querySelector('.check-icon');

            if (isSelected) {
                btn.className = 'bank-modal-item w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95 bg-indigo-600 text-white shadow-md';
                if (check) check.classList.remove('hidden');
            } else {
                btn.className = 'bank-modal-item w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer active:scale-95 bg-slate-50 text-slate-700 hover:bg-slate-100';
                if (check) check.classList.add('hidden');
            }
        });

        closeBankModal();
    }

    // --- 2. LOGIKA PERHITUNGAN & INPUT ---
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

    function calculateTotalTransfer() {
        let cashVal = parseInt(document.getElementById('cash_amount_input').value.replace(/\D/g, ''), 10) || 0;
        let adminVal = parseInt(document.getElementById('admin_fee_input').value.replace(/\D/g, ''), 10) || 0;

        document.getElementById('cash_amount_raw').value = cashVal;
        document.getElementById('admin_fee_raw').value = adminVal;

        let total = cashVal + adminVal;
        document.getElementById('total_transfer_display').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function prepareCleanNumbers(e) {
        let cashVal = document.getElementById('cash_amount_input').value.replace(/\D/g, '');
        let adminVal = document.getElementById('admin_fee_input').value.replace(/\D/g, '');

        document.getElementById('cash_amount_raw').value = cashVal;
        document.getElementById('admin_fee_raw').value = adminVal;

        if (!document.getElementById('form_payment_proof').value) {
            e.preventDefault();
            alert('Harap ambil foto bukti transfer pelanggan terlebih dahulu!');
        }
    }

    // --- 3. LOGIKA KAMERA ISOLATED (TANPA BENTROK) ---
    let qrisStream = null;

    async function startQrisCamera() {
        try {
            const video = document.getElementById('qris_video');
            const placeholder = document.getElementById('qris_placeholder');
            const resultDiv = document.getElementById('qris_result');

            qrisStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });

            video.srcObject = qrisStream;
            video.classList.remove('hidden');
            placeholder.classList.add('hidden');
            resultDiv.classList.add('hidden');

            document.getElementById('btn_start_cam').classList.add('hidden');
            
            const btnSnap = document.getElementById('btn_snap_qris');
            btnSnap.classList.remove('hidden');
            btnSnap.classList.add('flex');

            const btnReset = document.getElementById('btn_reset_qris');
            btnReset.classList.add('hidden');
            btnReset.classList.remove('flex');
        } catch (err) {
            alert('Tidak dapat mengakses kamera: ' + err.message);
        }
    }

    function take_qris_snapshot() {
        const video = document.getElementById('qris_video');
        const resultDiv = document.getElementById('qris_result');
        const hiddenInput = document.getElementById('form_payment_proof');

        if (!qrisStream) {
            alert('Silakan aktifkan kamera terlebih dahulu!');
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 720;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        hiddenInput.value = dataUrl;

        resultDiv.innerHTML = `<img src="${dataUrl}" class="w-full h-[300px] sm:h-[340px] object-cover rounded-2xl border-4 border-emerald-500 shadow-xl" />`;
        resultDiv.classList.remove('hidden');
        video.classList.add('hidden');

        if (qrisStream) {
            qrisStream.getTracks().forEach(track => track.stop());
            qrisStream = null;
        }

        const btnSnap = document.getElementById('btn_snap_qris');
        btnSnap.classList.add('hidden');
        btnSnap.classList.remove('flex');

        const btnReset = document.getElementById('btn_reset_qris');
        btnReset.classList.remove('hidden');
        btnReset.classList.add('flex');
    }

    function reset_qris_camera() {
        document.getElementById('form_payment_proof').value = '';
        document.getElementById('qris_result').classList.add('hidden');
        document.getElementById('qris_result').innerHTML = '';
        
        startQrisCamera();
    }
</script>
@endsection