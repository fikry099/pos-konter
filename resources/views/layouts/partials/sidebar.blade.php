<!-- SCRIPT ANTI-FLICKER SEBELUM RENDER (PRESISI 100% TENGAH) -->
<script>
    (function() {
        var savedState = localStorage.getItem('sidebar_state');
        var isSmallScreen = window.innerWidth < 768;
        
        if (savedState === 'collapsed' || (savedState === null && isSmallScreen)) {
            document.write('<style id="sidebar-temp-style">#sidebar{width:4.5rem !important; min-width:4.5rem !important; max-width:4.5rem !important;} .sidebar-text{display:none !important;} #sidebar nav a, #sidebar .header-container, #sidebar .profile-btn{justify-content:center !important; padding-left:0 !important; padding-right:0 !important;}</style>');
        }
    })();
</script>

<aside id="sidebar" class="bg-slate-900 text-white h-screen flex flex-col justify-between transition-[width] duration-300 z-30 shrink-0 w-48 select-none relative overflow-hidden">
    
    <!-- HEADER SIDEBAR & MENU ITEM -->
    <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
        
        <!-- HEADER LOGO & TOGGLE BUTTON -->
        <div class="h-16 px-2 flex items-center justify-between border-b border-slate-800 shrink-0 overflow-hidden header-container">
            <!-- LOGO: TRIGGER TOGGLE SIDEBAR -->
            <button type="button" onclick="toggleSidebar()" class="flex items-center justify-center overflow-hidden text-left cursor-pointer group shrink-0 w-full" title="Buka / Tutup Sidebar">
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('img/logo.png') }}" alt="W&A Cell Logo" class="w-8 h-8 rounded-xl object-contain drop-shadow group-hover:scale-105 transition-transform">
                </div>

                <div class="sidebar-text whitespace-nowrap overflow-hidden ml-2 flex-1">
                    <h1 class="font-black text-xs text-white leading-none tracking-wide">W & A CELL</h1>
                    <p class="text-[9px] font-bold text-indigo-400 mt-1 uppercase tracking-widest">POS System</p>
                </div>

                <!-- TOMBOL TOGGLE -->
                <div class="sidebar-toggle-btn sidebar-text text-slate-400 hover:text-white p-1 rounded-xl hover:bg-slate-800 transition cursor-pointer shrink-0">
                    <i id="sidebar_toggle_icon" class="fa-solid fa-angles-left text-xs"></i>
                </div>
            </button>
        </div>

        <!-- MENU NAVIGASI (SIMETRIS PERFECT) -->
        <nav class="p-2 space-y-1 overflow-y-auto flex-1 no-scrollbar overflow-x-hidden">
            @if(auth()->check() && auth()->user()->isOwner())
                <!-- OWNER MENU -->
                <a href="{{ route('dashboard') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Dashboard Analyst">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-pie text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Dashboard</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('products.index') || request()->routeIs('products.edit') || request()->routeIs('products.create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Kelola Katalog & Harga Modal">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-box text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Master Produk</span>
                </a>

                <a href="{{ route('owner.monitoring') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('owner.monitoring') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Monitoring Absensi & Audit Toko">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clipboard-user text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Monitoring</span>
                </a>

                <a href="{{ route('owner.expenses.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('owner.expenses.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Pengeluaran & Pembelian Voucher Toko">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-wallet text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Pengeluaran Kas</span>
                </a>

                <a href="{{ route('owner.bookkeeping') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('owner.bookkeeping') || request()->routeIs('owner.attendances.detail') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Pembukuan & Rekap Bonus Karyawan">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-book text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Pembukuan</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('transactions.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Audit Riwayat Transaksi">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-receipt text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Riwayat Transaksi</span>
                </a>
            @else
                <!-- KARYAWAN MENU -->
                <a href="{{ route('pos.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('pos.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Transaksi POS">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cash-register text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Transaksi POS</span>
                </a>

                <a href="{{ route('cash_out.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('cash_out.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Layanan Tarik Tunai">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-hand-holding-dollar text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Tarik Tunai</span>
                </a>

                <a href="{{ route('products.reorder') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('products.reorder') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Order Voucher Harian">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cart-flatbed text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Order Voucher</span>
                </a>

                <a href="{{ route('products.restock.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('products.restock.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Restok Barang Masuk">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-boxes-packing text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Restok Barang</span>
                </a>

                <a href="{{ route('shifts.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('shifts.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Shift & Absensi">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-clock text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Shift & Absensi</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('transactions.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Riwayat Transaksi">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-receipt text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Riwayat Transaksi</span>
                </a>

                <a href="{{ route('expenses.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('expenses.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Pengeluaran Kas Toko">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-wallet text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Pengeluaran Kas</span>
                </a>

                <a href="{{ route('returns.index') }}" class="flex items-center p-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('returns.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition group" title="Retur & Penukaran">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-rotate-left text-sm group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="sidebar-text whitespace-nowrap ml-2">Retur & Penukaran</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- FOOTER PROFILE USER -->
    <div class="p-2 border-t border-slate-800 shrink-0 relative overflow-hidden">
        <button type="button" onclick="toggleProfilePopover()" class="w-full flex items-center justify-between p-1 rounded-xl hover:bg-slate-800/80 transition cursor-pointer group profile-btn">
            <div class="flex items-center justify-center overflow-hidden min-w-0">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-xs shrink-0 border border-indigo-500/30 shadow-inner group-hover:border-indigo-400 transition">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="sidebar-text overflow-hidden leading-snug text-left ml-2">
                    <p class="text-xs font-extrabold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-[9px] capitalize font-medium">
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

<!-- CSS MEMAKSA SETIAP IKON TERTATA LURUS DI TENGAH SAAT SIDEBAR COLLAPSED -->
<style>
    #sidebar.collapsed {
        width: 4.5rem !important;
        min-width: 4.5rem !important;
        max-width: 4.5rem !important;
    }
    #sidebar.collapsed .sidebar-text {
        display: none !important;
    }
    #sidebar.collapsed nav {
        padding-left: 0.375rem !important;
        padding-right: 0.375rem !important;
    }
    #sidebar.collapsed nav a {
        justify-content: center !important;
        padding: 0.375rem 0 !important;
        width: 100% !important;
    }
    #sidebar.collapsed .header-container button,
    #sidebar.collapsed .profile-btn {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 100% !important;
    }
</style>

<!-- INCLUDE PARTIAL MODAL & SCRIPT JAVASCRIPT -->
@include('layouts.partials.profile-modal')
<script src="{{ asset('js/sidebar-profile.js') }}"></script>