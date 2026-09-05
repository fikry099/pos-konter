<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    @stack('styles')
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

    <!-- SCRIPT TOGGLE SIDEBAR -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let savedState = localStorage.getItem('sidebar_state');
            let sidebar = document.getElementById('sidebar');
            let icon = document.getElementById('sidebar_toggle_icon');
            let texts = document.querySelectorAll('.sidebar-text');
            let headerContainer = sidebar ? sidebar.querySelector('.h-16') : null;

            if (savedState === 'collapsed' && sidebar) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');

                if (icon) {
                    icon.classList.remove('fa-angles-left');
                    icon.classList.add('fa-angles-right');
                }
                if (headerContainer) {
                    headerContainer.classList.remove('justify-between');
                    headerContainer.classList.add('justify-center');
                }
                texts.forEach(el => el.classList.add('hidden'));
            }
        });

        function toggleSidebar() {
            let sidebar = document.getElementById('sidebar');
            let backdrop = document.getElementById('sidebar_backdrop');
            let icon = document.getElementById('sidebar_toggle_icon');
            let texts = document.querySelectorAll('.sidebar-text');
            let headerContainer = sidebar ? sidebar.querySelector('.h-16') : null;
            let tempStyle = document.getElementById('sidebar-temp-style');

            if (!sidebar) return;

            if (tempStyle) tempStyle.remove();

            let isCollapsed = sidebar.classList.contains('w-20');

            if (isCollapsed) {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                
                if (backdrop) backdrop.classList.add('hidden');

                if (icon) {
                    icon.classList.remove('fa-angles-right');
                    icon.classList.add('fa-angles-left');
                }
                if (headerContainer) {
                    headerContainer.classList.remove('justify-center');
                    headerContainer.classList.add('justify-between');
                }

                texts.forEach(el => {
                    el.style.removeProperty('display');
                    el.classList.remove('hidden');
                });

                localStorage.setItem('sidebar_state', 'expanded');
            } else {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                
                if (backdrop) backdrop.classList.add('hidden');

                if (icon) {
                    icon.classList.remove('fa-angles-left');
                    icon.classList.add('fa-angles-right');
                }
                if (headerContainer) {
                    headerContainer.classList.remove('justify-between');
                    headerContainer.classList.add('justify-center');
                }

                texts.forEach(el => el.classList.add('hidden'));

                localStorage.setItem('sidebar_state', 'collapsed');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>