<!-- SCRIPT ANTI-FLICKER SEBELUM RENDER -->
<script>
    (function() {
        var savedState = localStorage.getItem('sidebar_state');
        var isSmallScreen = window.innerWidth < 768;
        
        if (savedState === 'collapsed' || (savedState === null && isSmallScreen)) {
            document.write('<style id="sidebar-temp-style">#sidebar{width:5rem !important;} .sidebar-text{display:none !important;}</style>');
        }
    })();
</script>

<aside id="sidebar" class="bg-slate-900 text-white h-screen flex flex-col justify-between transition-all duration-300 z-30 shrink-0 w-64 select-none relative">
    
    <!-- HEADER SIDEBAR & MENU ITEM -->
    <div class="flex flex-col flex-1 min-h-0">
        
        <!-- HEADER LOGO & TOGGLE BUTTON -->
        <div class="h-16 px-3 flex items-center justify-between border-b border-slate-800 shrink-0 overflow-hidden">
            <!-- LOGO: TRIGGER TOGGLE SIDEBAR -->
            <button type="button" onclick="toggleSidebar()" class="flex items-center overflow-hidden text-left cursor-pointer group shrink-0" title="Buka / Tutup Sidebar">
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('img/logo.png') }}" alt="W&A Cell Logo" class="w-9 h-9 rounded-xl object-contain drop-shadow group-hover:scale-105 transition-transform">
                </div>

                <div class="sidebar-text whitespace-nowrap overflow-hidden ml-3">
                    <h1 class="font-black text-sm text-white leading-none tracking-wide">W & A CELL</h1>
                    <p class="text-[10px] font-bold text-indigo-400 mt-1 uppercase tracking-widest">POS System</p>
                </div>
            </button>

            <!-- TOMBOL TOGGLE (HILANG SAAT DITUTUP VIA CLASS 'sidebar-text') -->
            <button type="button" onclick="toggleSidebar()" class="sidebar-toggle-btn sidebar-text text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition cursor-pointer shrink-0" title="Tutup Sidebar">
                <i id="sidebar_toggle_icon" class="fa-solid fa-angles-left text-sm"></i>
            </button>
        </div>

        <!-- MENU NAVIGASI (IKON DIPAKSA KONSISTEN DI TENGAH W-10) -->
        <nav class="p-3 space-y-1.5 overflow-y-auto flex-1 no-scrollbar">
            @if(auth()->check() && auth()->user()->isOwner())
                <!-- OWNER MENU -->
                <a href="{{ route('dashboard') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Dashboard Analyst">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-pie text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Dashboard Analyst</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('products.index') || request()->routeIs('products.edit') || request()->routeIs('products.create') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Kelola Katalog & Harga Modal">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-boxes-stacked text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Master Produk</span>
                </a>

                <a href="{{ route('owner.monitoring') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('owner.monitoring') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Monitoring Absensi & Audit Toko">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clipboard-user text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Monitoring Absensi</span>
                </a>

                <a href="{{ route('owner.bookkeeping') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('owner.bookkeeping') || request()->routeIs('owner.attendances.detail') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Pembukuan & Rekap Bonus Karyawan">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-calculator text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Pembukuan & Bonus</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('transactions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Audit Riwayat Transaksi">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-receipt text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Riwayat Transaksi</span>
                </a>
            @else
                <!-- KARYAWAN MENU -->
                <a href="{{ route('pos.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('pos.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Transaksi POS">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cash-register text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Transaksi POS</span>
                </a>

                <a href="{{ route('products.reorder') }}" class="flex items-center justify-between p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('products.reorder') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Order Voucher Harian">
                    <div class="flex items-center overflow-hidden">
                        <div class="w-10 h-10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-cart-flatbed text-base group-hover:scale-110 transition-transform"></i>
                        </div>
                        <span class="sidebar-text whitespace-nowrap ml-2">Order Voucher</span>
                    </div>
                    <span class="sidebar-text bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[9px] font-extrabold px-1.5 py-0.5 rounded-md uppercase shrink-0 mr-1">
                        Order
                    </span>
                </a>

                <a href="{{ route('products.restock.index') }}" class="flex items-center justify-between p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('products.restock.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Restok Barang Masuk">
                    <div class="flex items-center overflow-hidden">
                        <div class="w-10 h-10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-boxes-packing text-base group-hover:scale-110 transition-transform"></i>
                        </div>
                        <span class="sidebar-text whitespace-nowrap ml-2">Restok Barang</span>
                    </div>
                    <span class="sidebar-text bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[9px] font-extrabold px-1.5 py-0.5 rounded-md uppercase shrink-0 mr-1">
                        Input
                    </span>
                </a>

                <a href="{{ route('shifts.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('shifts.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Shift & Absensi">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-clock text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Shift & Absensi</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('transactions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Riwayat Transaksi">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-receipt text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Riwayat Transaksi</span>
                </a>

                <a href="{{ route('expenses.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('expenses.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Pengeluaran Kas Toko">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-wallet text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Pengeluaran Kas</span>
                </a>

                <a href="{{ route('returns.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('returns.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Retur & Penukaran">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-rotate-left text-base group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Retur & Penukaran</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- FOOTER PROFILE USER -->
    <div class="p-3 border-t border-slate-800 shrink-0 relative">
        <button type="button" onclick="toggleProfilePopover()" class="w-full flex items-center justify-between p-1 rounded-xl hover:bg-slate-800/80 transition cursor-pointer group">
            <div class="flex items-center overflow-hidden min-w-0">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-xs shrink-0 border border-indigo-500/30 shadow-inner group-hover:border-indigo-400 transition">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="sidebar-text overflow-hidden leading-snug text-left ml-3">
                    <p class="text-xs font-extrabold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-[10px] capitalize font-medium">
                        @if(auth()->check() && auth()->user()->isOwner())
                            <span class="text-indigo-400 font-bold">Owner / Pemilik</span>
                        @else
                            <span class="text-emerald-400 font-bold">Karyawan / Kasir</span>
                        @endif
                    </p>
                </div>
            </div>
            <i class="fa-solid fa-ellipsis-vertical text-slate-400 group-hover:text-white text-xs sidebar-text pr-1"></i>
        </button>

        <!-- POPOVER MENU -->
        <div id="profile_popover" class="hidden absolute bottom-14 left-4 sm:left-14 w-48 bg-slate-800 border border-slate-700/80 rounded-2xl shadow-2xl p-1.5 z-9999 transition-all transform scale-95 opacity-0 origin-bottom-left space-y-1">
            <button type="button" onclick="openProfileModal()" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-slate-200 hover:bg-indigo-600 hover:text-white transition flex items-center space-x-2.5 cursor-pointer">
                <i class="fa-solid fa-user-gear text-indigo-400 text-xs"></i>
                <span>Profil & Cabang</span>
            </button>
            <div class="border-t border-slate-700/60 my-1"></div>
            <button type="button" onclick="confirmLogout()" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-600 hover:text-white transition flex items-center space-x-2.5 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                <span>Keluar / Logout</span>
            </button>
        </div>
    </div>

    <!-- FORM LOGOUT TERSEMBUNYI -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
</aside>

<!-- CSS KUNCI SIMETRIS KANAN DAN KIRI -->
<style>
    #sidebar.collapsed {
        width: 5rem !important; /* Dihitung matematis: padding p-3 (0.75rem / 12px) + w-10 (2.5rem / 40px) + padding p-3 (0.75rem / 12px) + border = 80px (5rem) */
    }
    #sidebar.collapsed .sidebar-text {
        display: none !important;
    }
</style>

<!-- INCLUDE PARTIAL MODAL & SCRIPT JAVASCRIPT -->
@include('layouts.partials.profile-modal')
<script src="{{ asset('js/sidebar-profile.js') }}"></script>