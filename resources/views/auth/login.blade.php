@extends('layouts.app')

@section('content')
<!-- CONTAINER UTAMA -->
<div class="h-screen w-full flex items-center justify-center p-4 relative overflow-hidden bg-slate-950">
    
    <!-- 1. BACKGROUND ANIMATED -->
    <div class="fixed inset-0 w-full h-full pointer-events-none z-0 overflow-hidden flex items-center justify-center">
        <div class="w-[115%] h-[115%] bg-cover bg-center bg-no-repeat animate-sway-slow opacity-60"
            style="background-image: url('{{ asset('img/bg-login.webp') }}');">
        </div>
    </div>

    <!-- 2. OVERLAY GELAP -->
    <div class="fixed inset-0 bg-slate-950/70 z-0 pointer-events-none"></div>

    <!-- 3. CARD FORM LOGIN TUNGGAL (CENTERED) -->
    <div class="relative z-10 w-full max-w-sm sm:max-w-md bg-white rounded-3xl border border-white/20 shadow-2xl overflow-hidden p-6 sm:p-8 my-auto space-y-5 transition-all">
        
        <!-- HEADER LOGO & JUDUL -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center p-2.5 rounded-2xl bg-indigo-50/50 border border-indigo-100/50 shadow-xs">
                <img src="{{ asset('img/logo-nobg.png') }}" alt="W&A Cell Logo" class="h-14 w-auto object-contain drop-shadow-sm">
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight leading-none">W & A CELL</h2>
                <p class="text-[10px] font-extrabold tracking-widest text-indigo-600 uppercase mt-1">VOUCHER dan CELL SYSTEM</p>
            </div>
            <p class="text-xs text-slate-500 font-medium pt-1">Silakan masuk untuk mengoperasikan sistem kasir.</p>
        </div>

        <!-- NOTIFIKASI ERROR / STATUS -->
        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-3.5 py-2.5 rounded-2xl text-xs font-bold flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any() && !$errors->has('username') && !$errors->has('password'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3.5 py-2.5 rounded-2xl text-xs font-bold flex items-center space-x-2">
                <i class="fa-solid fa-triangle-exclamation text-sm shrink-0"></i>
                <span>Username atau password salah.</span>
            </div>
        @endif

        <!-- FORM LOGIN -->
        <form class="space-y-3.5" action="{{ route('login') }}" method="POST">
            @csrf

            <!-- INPUT USERNAME / EMAIL -->
            <div>
                <label for="username" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                    Username / Email
                </label>
                <div class="relative rounded-2xl shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user text-xs sm:text-sm"></i>
                    </div>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                        placeholder="Masukkan username atau email"
                        class="w-full pl-10 pr-3.5 py-2.5 text-xs sm:text-sm bg-slate-50/80 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-600 focus:bg-white font-medium transition text-slate-800 @error('username') border-rose-500 focus:ring-rose-500 @enderror">
                </div>
                @error('username')
                    <p class="mt-1 text-[11px] text-rose-500 font-bold flex items-center">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- INPUT PASSWORD -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password_input" class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition">
                            Lupa Password?
                        </a>
                    @endif
                </div>
                <div class="relative rounded-2xl shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-xs sm:text-sm"></i>
                    </div>
                    
                    <input id="password_input" name="password" type="password" required
                        placeholder="••••••••"
                        class="w-full pl-10 pr-10 py-2.5 text-xs sm:text-sm bg-slate-50/80 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-600 focus:bg-white font-medium transition text-slate-800 @error('password') border-rose-500 focus:ring-rose-500 @enderror">
                    
                    <!-- TOGGLE PASSWORD -->
                    <button type="button" onclick="togglePasswordVisibility()" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition focus:outline-none cursor-pointer">
                        <i id="password_eye_icon" class="fa-solid fa-eye text-xs sm:text-sm"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-[11px] text-rose-500 font-bold flex items-center">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- REMEMBER ME -->
            <div class="flex items-center justify-between pt-0.5">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" id="remember" 
                        class="w-4 h-4 text-indigo-600 bg-slate-100 border-slate-300 rounded-md focus:ring-indigo-500 focus:ring-2 transition cursor-pointer">
                    <span class="text-xs font-bold text-slate-600">Ingat Saya</span>
                </label>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="pt-1">
                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-extrabold py-3 px-4 rounded-2xl shadow-lg shadow-indigo-600/25 transition duration-150 ease-in-out flex items-center justify-center space-x-2 text-xs sm:text-sm cursor-pointer">
                    <span>Masuk ke Sistem</span>
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs sm:text-sm"></i>
                </button>
            </div>
        </form>

        <!-- FOOTER -->
        <div class="border-t border-slate-100 pt-3 text-center">
            <p class="text-[10px] text-slate-400 font-medium">
                W&A POS v1.0 &copy; apies {{ date('Y') }}
            </p>
        </div>

    </div>
</div>

<style>
    @keyframes sway-slow {
        0% { transform: translate(0px, 0px) rotate(0deg) scale(1); }
        50% { transform: translate(15px, -12px) rotate(2.5deg) scale(1.03); }
        100% { transform: translate(0px, 0px) rotate(0deg) scale(1); }
    }
    .animate-sway-slow { animation: sway-slow 16s ease-in-out infinite; }
</style>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password_input');
        const eyeIcon = document.getElementById('password_eye_icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash', 'text-indigo-600');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash', 'text-indigo-600');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection