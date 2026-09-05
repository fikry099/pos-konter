<!-- MODAL ABSENSI KARYAWAN SUSULAN (MEMANJANG KE KANAN - LANDSCAPE) -->
<div id="joinShiftModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-200 transition-all my-auto">
        
        <!-- HEADER MODAL -->
        <div class="bg-indigo-600 px-6 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm sm:text-base flex items-center">
                <i class="fa-solid fa-user-plus mr-2.5 text-lg"></i>
                <span>Absensi Karyawan Susulan</span>
            </h3>
            <button type="button" onclick="closeJoinShiftModal()" class="text-indigo-200 hover:text-white p-1 rounded-lg transition cursor-pointer">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form action="{{ route('shifts.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="photo" id="modal_photo_data" required>

            <!-- GRID 2 KOLOM HORIZONTAL (SISI KIRI: NAMA, SISI KANAN: WEBCAM) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                
                <!-- SISI KIRI: SELEKSI KARYAWAN -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        1. Pilih Nama Karyawan
                    </label>
                    
                    <div class="bg-slate-50 border border-slate-300 rounded-2xl p-2.5 max-h-56 overflow-y-auto space-y-2">
                        @forelse($users as $user)
                            <label class="flex items-center space-x-3 p-2.5 bg-white rounded-xl cursor-pointer transition border border-slate-200 hover:border-indigo-500 hover:shadow-xs">
                                <input type="radio" name="user_id" value="{{ $user->id }}" required class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-2 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-800">{{ $user->name }}</span>
                            </label>
                        @empty
                            <div class="py-6 text-center text-slate-400 text-xs font-semibold">
                                <i class="fa-solid fa-user-check text-2xl mb-1 text-slate-300 block"></i>
                                Semua karyawan telah absen pada shift aktif ini.
                            </div>
                        @endforelse
                    </div>

                    <p class="text-[11px] text-slate-400 font-medium leading-relaxed">
                        * Pilihlah nama akun Anda sendiri untuk mendaftarkan jam presensi masuk ke shift yang sedang berjalan.
                    </p>
                </div>

                <!-- SISI KANAN: WEBCAM ABSENSI SELFIE -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        2. Foto Absensi Wajah
                    </label>

                    <div class="bg-slate-50 p-3 rounded-2xl border border-dashed border-slate-300 flex flex-col items-center">
                        <div class="w-full h-40 bg-slate-200 rounded-xl overflow-hidden flex items-center justify-center border border-slate-300 relative shadow-inner">
                            <video id="modal_webcam_video" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas id="modal_webcam_canvas" class="hidden"></canvas>
                            <img id="modal_photo_preview" class="hidden w-full h-full object-cover">
                        </div>

                        <div class="mt-3 flex space-x-2 w-full">
                            <button type="button" onclick="take_modal_snapshot()" id="modal_btn_take" class="flex-1 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs py-2 rounded-xl font-bold transition shadow-md flex items-center justify-center cursor-pointer">
                                <i class="fa-solid fa-camera mr-1.5"></i> Ambil Foto
                            </button>
                            <button type="button" onclick="reset_modal_webcam()" id="modal_btn_reset" class="hidden flex-1 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-xs py-2 rounded-xl font-bold transition items-center justify-center shadow-md cursor-pointer">
                                <i class="fa-solid fa-rotate-left mr-1.5"></i> Foto Ulang
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER TOMBOL AKSI -->
            <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeJoinShiftModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-300 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-extrabold rounded-xl shadow-md shadow-indigo-200 transition flex items-center space-x-1.5 cursor-pointer">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Simpan Absen Susulan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let modalVideo = document.getElementById('modal_webcam_video');
    let modalCanvas = document.getElementById('modal_webcam_canvas');
    let modalPreview = document.getElementById('modal_photo_preview');
    let modalPhotoInput = document.getElementById('modal_photo_data');
    let modalBtnTake = document.getElementById('modal_btn_take');
    let modalBtnReset = document.getElementById('modal_btn_reset');

    function openJoinShiftModal() {
        document.getElementById('joinShiftModal').classList.remove('hidden');
        initModalCamera();
    }

    function closeJoinShiftModal() {
        document.getElementById('joinShiftModal').classList.add('hidden');
    }

    function initModalCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
                .then(function(stream) {
                    modalVideo.srcObject = stream;
                    modalVideo.play();
                })
                .catch(function(err) {
                    console.error("Gagal kamera modal:", err);
                });
        }
    }

    function take_modal_snapshot() {
        if (!modalVideo.srcObject) return;

        modalCanvas.width = modalVideo.videoWidth || 640;
        modalCanvas.height = modalVideo.videoHeight || 480;
        let context = modalCanvas.getContext('2d');
        context.drawImage(modalVideo, 0, 0, modalCanvas.width, modalCanvas.height);

        let dataUrl = modalCanvas.toDataURL('image/jpeg');
        modalPhotoInput.value = dataUrl;

        modalPreview.src = dataUrl;
        modalPreview.classList.remove('hidden');
        modalVideo.classList.add('hidden');

        modalBtnTake.classList.add('hidden');
        modalBtnReset.classList.remove('hidden');
        modalBtnReset.classList.add('flex');
    }

    function reset_modal_webcam() {
        modalPhotoInput.value = '';
        modalPreview.classList.add('hidden');
        modalVideo.classList.remove('hidden');

        modalBtnTake.classList.remove('hidden');
        modalBtnReset.classList.add('hidden');
        modalBtnReset.classList.remove('flex');
    }
</script>