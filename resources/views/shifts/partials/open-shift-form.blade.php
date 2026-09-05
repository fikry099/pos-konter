<div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="bg-indigo-600 px-6 py-5 text-white flex items-center justify-between">
        <h2 class="font-black text-base flex items-center">
            <span class="w-10 h-10 rounded-2xl bg-indigo-500/60 flex items-center justify-center mr-3 border border-indigo-400/40 text-base">
                <i class="fa-solid fa-user-clock"></i>
            </span>
            <span>{{ isset($activeShift) && $activeShift ? 'Absensi Karyawan Susulan' : 'Form Buka Shift Baru' }}</span>
        </h2>

        @if(isset($activeShift) && $activeShift)
            <span class="bg-emerald-500/30 text-emerald-100 text-xs font-mono font-bold px-3 py-1 rounded-full border border-emerald-400/30">
                ● Shift #{{ $activeShift->id }} Sedang Aktif
            </span>
        @endif
    </div>
    
    <form action="{{ route('shifts.store') }}" method="POST" class="p-8 space-y-8">
        @csrf
        <input type="hidden" name="photo" id="photo_data" required>

        @if(!isset($activeShift) || !$activeShift)
            <!-- Hidden input murni angka modal awal -->
            <input type="hidden" name="cash_initial" id="raw_cash_initial" value="0">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <!-- SISI KIRI: INPUT FORM -->
            <div class="space-y-6">
                
                <!-- PILIH 1 KARYAWAN BERTUGAS -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pilih Karyawan Absen
                    </label>
                    <div class="bg-slate-50 border border-slate-300 rounded-2xl p-3 max-h-48 overflow-y-auto space-y-2">
                        @forelse($users as $user)
                            @if($user->has_clocked_in_today)
                                <!-- TAMPILAN ABU-ABU (DISABLED - SUDAH ABSEN HARI INI) -->
                                <label class="flex items-center justify-between p-2.5 bg-slate-100 rounded-xl border border-slate-200 opacity-60 cursor-not-allowed select-none">
                                    <div class="flex items-center space-x-3">
                                        <input type="radio" name="user_id" value="{{ $user->id }}" disabled class="w-4 h-4 text-slate-400 border-slate-300 focus:ring-0 cursor-not-allowed">
                                        <span class="text-sm font-bold text-slate-500 line-through decoration-slate-400">{{ $user->name }}</span>
                                    </div>
                                    <span class="text-[10px] font-extrabold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md flex items-center">
                                        <i class="fa-solid fa-circle-check text-emerald-600 mr-1 text-[9px]"></i> Sudah Absen Hari Ini
                                    </span>
                                </label>
                            @else
                                <!-- TAMPILAN NORMAL (BISA DIKLIK) -->
                                <label class="flex items-center space-x-3 p-2.5 bg-white rounded-xl cursor-pointer transition border border-slate-200 hover:border-indigo-500 hover:shadow-xs">
                                    <input type="radio" name="user_id" value="{{ $user->id }}" required class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-2 focus:ring-indigo-500">
                                    <span class="text-sm font-bold text-slate-800">{{ $user->name }}</span>
                                </label>
                            @endif
                        @empty
                            <div class="py-4 text-center text-slate-400 text-xs font-semibold">
                                <i class="fa-solid fa-user-check text-xl mb-1 text-slate-300 block"></i>
                                Tidak ada data karyawan aktif.
                            </div>
                        @endforelse
                    </div>
                    <span class="text-xs text-slate-400 mt-2 block font-medium">
                        * Pilih nama Anda sendiri untuk mencatat absensi masuk.
                    </span>
                </div>

                <!-- INPUT MODAL AWAL (HANYA TAMPIL JIKA SHIFT BELUM BUKA) -->
                @if(!isset($activeShift) || !$activeShift)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Modal Uang Tunai Awal (Rp)</label>
                        <div class="relative rounded-2xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 text-sm font-black">
                                Rp
                            </div>
                            <input type="text" id="formatted_cash_initial" oninput="formatShiftCurrency(this, 'raw_cash_initial')" required placeholder="Contoh: 200.000" autocomplete="off" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 text-slate-900 border rounded-2xl border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition text-sm font-black placeholder-slate-400">
                        </div>
                        <span class="text-xs text-slate-400 mt-2 block font-medium">* Nominal uang kembalian di laci kasir saat shift dimulai.</span>
                    </div>
                @else
                    <!-- INFORMASI MODAL SAMA SAAT JOIN SHIFT -->
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl space-y-1">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Modal Uang Awal Laci</span>
                        <span class="text-base font-black text-indigo-800 font-mono">
                            Rp {{ number_format($activeShift->cash_initial, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-indigo-600 block mt-1 font-medium">
                            * Modal awal sudah diinput oleh kasir pertama.
                        </span>
                    </div>
                @endif

            </div>

            <!-- SISI KANAN: WEBCAM ABSENSI -->
            <div class="flex flex-col items-center justify-center bg-slate-50 p-6 rounded-2xl border border-dashed border-slate-300">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Foto Absensi Wajah</label>
                
                <div class="w-full h-56 bg-slate-200 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-300 relative shadow-inner">
                    <video id="webcam_video" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="webcam_canvas" class="hidden"></canvas>
                    <img id="photo_preview" class="hidden w-full h-full object-cover">
                </div>

                <div class="mt-4 flex space-x-3">
                    <button type="button" onclick="take_snapshot()" id="btn_take" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs px-5 py-2.5 rounded-xl font-bold transition shadow-md flex items-center">
                        <i class="fa-solid fa-camera mr-2"></i> Ambil Foto
                    </button>
                    <button type="button" onclick="reset_webcam()" id="btn_reset" class="hidden bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-xs px-5 py-2.5 rounded-xl font-bold transition items-center shadow-md">
                        <i class="fa-solid fa-rotate-left mr-2"></i> Foto Ulang
                    </button>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold px-6 py-3.5 rounded-2xl text-sm transition shadow-lg shadow-indigo-200 flex items-center">
                <i class="fa-solid fa-play mr-2 text-xs"></i> 
                <span>{{ isset($activeShift) && $activeShift ? 'Absen Masuk & Bergabung' : 'Buka Shift & Mulai Transaksi' }}</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let video = document.getElementById('webcam_video');
    let canvas = document.getElementById('webcam_canvas');
    let preview = document.getElementById('photo_preview');
    let photoInput = document.getElementById('photo_data');
    let btnTake = document.getElementById('btn_take');
    let btnReset = document.getElementById('btn_reset');

    document.addEventListener("DOMContentLoaded", function() {
        initCamera();
    });

    function initCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: "user", // Memaksa kamera depan/selfie tablet
                    width: { ideal: 640 }, 
                    height: { ideal: 480 } 
                } 
            })
                .then(function(stream) {
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(err) {
                    alert("Gagal mengakses kamera: Pastikan Anda telah memberikan izin (Allow) pada browser!");
                    console.error("Error Webcam:", err);
                });
        }
    }

    function take_snapshot() {
        if (!video.srcObject) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        let context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        let dataUrl = canvas.toDataURL('image/jpeg');
        photoInput.value = dataUrl;

        preview.src = dataUrl;
        preview.classList.remove('hidden');
        video.classList.add('hidden');

        btnTake.classList.add('hidden');
        btnReset.classList.remove('hidden');
        btnReset.classList.add('flex');
    }

    function reset_webcam() {
        photoInput.value = '';
        preview.classList.add('hidden');
        video.classList.remove('hidden');

        btnTake.classList.remove('hidden');
        btnReset.classList.add('hidden');
        btnReset.classList.remove('flex');
    }

    function formatShiftCurrency(input, targetHiddenId) {
        let rawValue = input.value.replace(/\D/g, '');
        let hiddenInput = document.getElementById(targetHiddenId);
        if (hiddenInput) hiddenInput.value = rawValue;

        if (rawValue === '') {
            input.value = '';
            return;
        }

        input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
    }
</script>
@endpush