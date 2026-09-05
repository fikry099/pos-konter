<!-- MODAL PROFIL USER, CABANG & GANTI PASSWORD (COMPACT NO-SCROLL) -->
<div id="profile_modal" class="fixed inset-0 z-[10000] hidden bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 transition-all duration-300">
    <div id="profile_modal_card" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        
        <!-- HEADER MODAL COMPACT DENGAN DYNAMIC TITLE -->
        <div class="px-5 py-3.5 bg-gradient-to-r from-purple-50/80 via-indigo-50/40 to-slate-50 flex items-center justify-between shrink-0 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-md shadow-indigo-200">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                    <!-- PENGONDISIAN JUDUL MODAL BERDASARKAN ROLE -->
                    <h3 class="font-black text-sm text-slate-800 leading-tight">
                        @if(auth()->check() && auth()->user()->isOwner())
                            Pengaturan Akun Owner
                        @else
                            Pengaturan Akun Cabang
                        @endif
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium">
                        @if(auth()->check() && auth()->user()->isOwner())
                            Informasi pemilik & hak akses sistem POS
                        @else
                            Informasi akun operasional POS
                        @endif
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeProfileModal()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- CONTENT MODAL COMPACT -->
        <div class="p-4 sm:p-5 space-y-4">
            
            <!-- SECTION 1: INFORMASI PENGGUNA -->
            <div class="space-y-2">
                <div class="flex items-center space-x-1.5">
                    <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">
                        Informasi Pengguna
                    </h4>
                </div>

                <div class="bg-slate-50/70 border border-slate-200/60 rounded-xl p-3 text-xs space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 block">Nama Lengkap</span>
                            <span class="font-extrabold text-xs text-slate-800 truncate block">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 block">Email / Username</span>
                            <span class="font-extrabold text-xs text-slate-800 truncate block">{{ auth()->user()->email }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 block">Peran / Role</span>
                            <span class="font-black text-[10px] text-indigo-600 uppercase tracking-wide block">{{ auth()->user()->role }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 block">Cabang Penugasan</span>
                            <span class="font-black text-[10px] text-emerald-600 uppercase tracking-wide truncate block">
                                {{ auth()->user()->store?->name ?? 'FLEKSIBEL / SEMUA CABANG' }}
                            </span>
                        </div>
                    </div>

                    @if(auth()->user()->store)
                        <div class="pt-1.5 border-t border-slate-200/60 flex items-center space-x-2">
                            <i class="fa-solid fa-location-dot text-purple-600 text-[10px] shrink-0"></i>
                            <span class="text-[10px] font-bold text-slate-700 truncate">
                                Alamat: <span class="font-medium text-slate-600">{{ auth()->user()->store->address ?? '-' }}</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SECTION 2: GANTI PASSWORD-->
            <div class="space-y-2 pt-1 border-t border-slate-100">
                <div class="flex items-center space-x-1.5">
                    <div class="w-5 h-5 rounded-md bg-amber-50 text-amber-500 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-wider">
                            Ganti Password
                        </h4>
                    </div>
                </div>

                <form id="change_password_form" onsubmit="submitChangePassword(event)" class="space-y-2.5">
                    @csrf
                    @method('PUT')

                    <!-- PASSWORD SAAT INI -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-0.5">
                            Password Saat Ini
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                            </div>
                            <input type="password" id="current_password" name="current_password" required placeholder="••••••••" 
                                class="w-full pl-8 pr-8 py-1.5 text-xs bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:bg-white font-medium transition text-slate-800">
                            <button type="button" onclick="togglePasswordInput('current_password', 'eye_icon_curr')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-indigo-600 transition focus:outline-none">
                                <i id="eye_icon_curr" class="fa-solid fa-eye text-[10px]"></i>
                            </button>
                        </div>
                    </div>

                    <!-- GRID PASSWORD BARU & KONFIRMASI -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 mb-0.5">
                                Password Baru
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                </div>
                                <input type="password" id="new_password" name="password" required placeholder="••••••••" 
                                    class="w-full pl-8 pr-8 py-1.5 text-xs bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:bg-white font-medium transition text-slate-800">
                                <button type="button" onclick="togglePasswordInput('new_password', 'eye_icon_new')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-indigo-600 transition focus:outline-none">
                                    <i id="eye_icon_new" class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 mb-0.5">
                                Konfirmasi Password
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                </div>
                                <input type="password" id="new_password_confirmation" name="password_confirmation" required placeholder="••••••••" 
                                    class="w-full pl-8 pr-8 py-1.5 text-xs bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:bg-white font-medium transition text-slate-800">
                                <button type="button" onclick="togglePasswordInput('new_password_confirmation', 'eye_icon_conf')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-indigo-600 transition focus:outline-none">
                                    <i id="eye_icon_conf" class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL SIMPAN -->
                    <div class="pt-1">
                        <button type="submit" id="btn_save_password" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] text-white font-extrabold py-2 px-3 rounded-xl shadow-md shadow-indigo-600/20 transition duration-150 flex items-center justify-center space-x-1.5 text-xs cursor-pointer">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                            <span>Simpan Password Baru</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function togglePasswordInput(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash', 'text-indigo-600');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash', 'text-indigo-600');
            icon.classList.add('fa-eye');
        }
    }
</script>