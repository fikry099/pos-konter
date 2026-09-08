<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>POS WannCell</title>
    
    <!-- ICON BROWSER / FAVICON -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo-nobg.png') }}">
    
    <!-- PWA MANIFEST & CONFIGURATION -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="W&A POS">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-nobg.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- STYLE OVERRIDE GLOBAL (SIDEBAR & SWEETALERT Z-INDEX) -->
    <style>
        /* 1. SWEETALERT2 PALING DEPAN (DI ATAS MODAL & PANEL KERANJANG) */
        .swal2-container {
            z-index: 9999999 !important;
        }

        /* 2. KUNCI LEBAR PERMANEN SIDEBAR SAAT TERBUKA */
        #sidebar {
            width: 12rem !important; /* 192px / w-48 */
            min-width: 12rem !important;
            max-width: 12rem !important;
            transition: width 0.3s ease, min-width 0.3s ease, max-width 0.3s ease !important;
        }

        /* 3. KUNCI LEBAR PERMANEN SIDEBAR SAAT DITUTUP (COLLAPSED) */
        #sidebar.collapsed {
            width: 4.5rem !important; /* 72px */
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }

        /* 4. SEMBUNYIKAN TEKS SAAT SIDEBAR DITUTUP */
        #sidebar.collapsed .sidebar-text {
            display: none !important;
        }

        /* 5. PAKSA PRESISI TENGAH SEMUA IKON SAAT DITUTUP */
        #sidebar.collapsed nav {
            padding-left: 0.375rem !important;
            padding-right: 0.375rem !important;
        }
        
        #sidebar.collapsed nav a,
        #sidebar.collapsed .header-container button,
        #sidebar.collapsed .profile-btn {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            width: 100% !important;
        }

        #sidebar.collapsed nav a div {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
    </style>

    @stack('styles')
    <!-- ESC/POS ENCODER UTK PRINTER THERMAL BLUETOOTH -->
    <script src="https://cdn.jsdelivr.net/npm/esc-pos-encoder@1.3.0/dist/esc-pos-encoder.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased relative">

    {{-- KONDISI 1: HALAMAN LOGIN / GUEST --}}
    @if(request()->routeIs('login') || Auth::guest())

        <main class="min-h-screen flex items-center justify-center bg-slate-100">
            @yield('content')
        </main>

    {{-- KONDISI 2: HALAMAN UTAMA APLIKASI TERPROTEKSI --}}
    @else

        <div class="flex h-screen overflow-hidden relative">

            <!-- SIDEBAR KIRI -->
            @include('layouts.partials.sidebar')

            <!-- BACKDROP OVERLAY KHUSUS TABLET/MOBILE SAAT SIDEBAR DIBUKA -->
            <div id="sidebar_backdrop" 
                onclick="toggleSidebar()" 
                class="fixed inset-0 z-30 bg-slate-900/30 hidden transition-opacity duration-300 cursor-pointer">
            </div>

            <!-- AREA KONTEN UTAMA -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <!-- HEADER ATAS -->
                @include('layouts.partials.header')

                <!-- KONTEN HALAMAN UTAMA -->
                <main class="flex-1 overflow-y-auto p-3 sm:p-4 bg-slate-50 min-w-0">
                    <div class="max-w-7xl mx-auto space-y-3 sm:space-y-4">

                        @yield('content')
                        
                    </div>
                </main>

            </div>

        </div>

        <!-- FLOATING CHAT WIDGET AI -->
        @include('ai.index')

        <!-- POPOVER MENU PROFIL FLOATING (DI LUAR SIDEBAR AGAR BEBAS OVERFLOW) -->
        <div id="global_profile_popover" class="hidden fixed bottom-16 left-3 sm:left-14 w-48 bg-slate-800 border border-slate-700/80 rounded-2xl shadow-2xl p-1.5 z-[99999] space-y-1">
            <button type="button" onclick="openProfileModal(); hideProfilePopover();" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-slate-200 hover:bg-indigo-600 hover:text-white transition flex items-center space-x-2.5 cursor-pointer">
                <i class="fa-solid fa-user-gear text-indigo-400 text-xs"></i>
                <span>Profil & Cabang</span>
            </button>
            <div class="border-t border-slate-700/60 my-1"></div>
            <button type="button" onclick="confirmLogout(); hideProfilePopover();" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-600 hover:text-white transition flex items-center space-x-2.5 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                <span>Keluar / Logout</span>
            </button>
        </div>

    @endif

    <!-- INCLUDE ALERT HANDLER (TOAST NOTIFICATION) -->
    @include('layouts.partials.alerts')

    <!-- REGISTRASI SERVICE WORKER PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('PWA ServiceWorker terdaftar:', registration.scope);
                }, function(err) {
                    console.log('PWA ServiceWorker gagal:', err);
                });
            });
        }
    </script>

    <!-- SCRIPT TOGGLE SIDEBAR & GLOBAL POPOVER -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let savedState = localStorage.getItem('sidebar_state');
            let sidebar = document.getElementById('sidebar');
            let icon = document.getElementById('sidebar_toggle_icon');

            if (savedState === 'collapsed' && sidebar) {
                sidebar.classList.add('collapsed');

                if (icon) {
                    icon.classList.remove('fa-angles-left');
                    icon.classList.add('fa-angles-right');
                }
            }
        });

        function toggleSidebar() {
            let sidebar = document.getElementById('sidebar');
            let backdrop = document.getElementById('sidebar_backdrop');
            let icon = document.getElementById('sidebar_toggle_icon');
            let tempStyle = document.getElementById('sidebar-temp-style');

            if (!sidebar) return;

            if (tempStyle) tempStyle.remove();

            let isCollapsed = sidebar.classList.contains('collapsed');

            if (isCollapsed) {
                sidebar.classList.remove('collapsed');
                if (backdrop) backdrop.classList.add('hidden');

                if (icon) {
                    icon.classList.remove('fa-angles-right');
                    icon.classList.add('fa-angles-left');
                }

                localStorage.setItem('sidebar_state', 'expanded');
            } else {
                sidebar.classList.add('collapsed');
                if (backdrop) backdrop.classList.add('hidden');

                if (icon) {
                    icon.classList.remove('fa-angles-left');
                    icon.classList.add('fa-angles-right');
                }

                localStorage.setItem('sidebar_state', 'collapsed');
            }
        }

        // FUNGSI GLOBAL UNTUK KONTROL POPOVER PROFIL
        function toggleProfilePopover() {
            let popover = document.getElementById('global_profile_popover');
            if (popover) {
                popover.classList.toggle('hidden');
            }
        }

        function hideProfilePopover() {
            let popover = document.getElementById('global_profile_popover');
            if (popover) {
                popover.classList.add('hidden');
            }
        }

        // Sembunyikan popover saat mengklik di luar area popover
        document.addEventListener('click', function(e) {
            let popover = document.getElementById('global_profile_popover');
            let profileBtn = document.querySelector('.profile-btn');
            if (popover && !popover.classList.contains('hidden')) {
                if (!popover.contains(e.target) && (!profileBtn || !profileBtn.contains(e.target))) {
                    popover.classList.add('hidden');
                }
            }
        });

        function confirmLogout() {
            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: "Apakah Anda yakin ingin mengakhiri sesi kasir ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-5 py-2.5',
                    cancelButton: 'rounded-xl text-xs font-bold px-5 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('logout-form');
                    if (form) form.submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>