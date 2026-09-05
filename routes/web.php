<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\BookkeepingController;
use App\Http\Controllers\ProfileController;

// Middlewares
use App\Http\Middleware\EnsureShiftIsOpen;
use App\Http\Middleware\EnsureUserIsOwner;

/*
|--------------------------------------------------------------------------
| 1. RUTE AUTH (GUEST ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/', function () {
    return redirect()->route('shifts.index');
});

/*
|--------------------------------------------------------------------------
| 2. RUTE APLIKASI TERPROTEKSI (AKUN CABANG & OWNER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Self-Service Profile & Ganti Password
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Switcher Filter Cabang (Khusus Akses Owner via Navbar)
    Route::post('/stores/switch', [StoreController::class, 'switchStore'])->name('stores.switch');

    // Shift & Absensi Kasir
    Route::get('/shift', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shift/open', [ShiftController::class, 'store'])->name('shifts.store');
    Route::post('/shift/close/{id}', [ShiftController::class, 'closeShift'])->name('shifts.close');
    Route::post('/shift/switch/{id}', [ShiftController::class, 'switchShift'])->name('shifts.switch');

    // Restok & Reorder Voucher
    Route::get('/products/restock', [ProductController::class, 'restockView'])->name('products.restock.index');
    Route::post('/products/restock', [ProductController::class, 'processRestock'])->name('products.restock.process');
    Route::get('/products/reorder', [ProductController::class, 'reorderOrder'])->name('products.reorder');

    // Riwayat Transaksi & Audit Struk
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export-excel', [TransactionController::class, 'exportExcel'])->name('transactions.export_excel');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

    // Modul Pengeluaran Kas Operasional (Expenses)
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Asisten AI
    Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai.index');
    Route::post('/ai-assistant/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');

    // Modul Retur Barang 
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::get('/returns/search-transaction', [ReturnController::class, 'searchTransaction'])->name('returns.search');
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');

    /*
    |--------------------------------------------------------------------------
    | 3. ROUTE KHUSUS ROLE OWNER (MANAGEMENT, HPP, & DASHBOARD TOKO)
    |--------------------------------------------------------------------------
    */
    Route::middleware([EnsureUserIsOwner::class])->group(function () {
        
        // Dashboard Analisis Keuangan & Profits
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Monitoring Shift, Absensi, & Audit Laci Seluruh Toko
        Route::get('/owner/monitoring', [DashboardController::class, 'monitoring'])->name('owner.monitoring');

        // Pembukuan Keuangan, Rekap Bonus, & Detail Absensi Karyawan (Tab Baru)
        Route::get('/owner/bookkeeping', [BookkeepingController::class, 'index'])->name('owner.bookkeeping');
        Route::get('/owner/attendances/{user}', [BookkeepingController::class, 'userAttendanceDetail'])->name('owner.attendances.detail');

        // Master Produk & Katalog Terpusat (Pengaturan HPP & Harga Modal)
        Route::resource('products', ProductController::class)->except(['show']);
        
    });

    /*
    |--------------------------------------------------------------------------
    | 4. ROUTE POS (Dapat Diakses Akun Cabang & Owner saat Shift AKTIF)
    |--------------------------------------------------------------------------
    */
    Route::middleware([EnsureShiftIsOpen::class])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/cart/add', [PosController::class, 'addToCart'])->name('pos.cart.add');
        Route::post('/pos/cart/update/{key}', [PosController::class, 'updateCart'])->name('pos.cart.update');
        Route::get('/pos/cart/remove/{key}', [PosController::class, 'removeFromCart'])->name('pos.cart.remove');
        Route::get('/pos/cart/clear', [PosController::class, 'clearCart'])->name('pos.cart.clear');
        Route::post('/pos/checkout', [PosController::class, 'store'])->name('pos.checkout');
        Route::post('/pos/cart/assign-staff/{key}', [PosController::class, 'assignStaff'])->name('pos.cart.assign_staff');
        Route::get('/pos/products/by-category', [App\Http\Controllers\PosController::class, 'getProductsByCategory'])->name('pos.products.by-category');
    });

});