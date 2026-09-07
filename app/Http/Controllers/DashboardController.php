<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Shift;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Menampilkan Halaman Dashboard Pemilik Toko
     */
    public function index()
    {
        $activeStoreId = $this->getActiveStoreId(); // <--- Dapatkan ID Store Aktif

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. STATISTIK HARI INI (FILTER BY STORE_ID)
        $todaySales = Transaction::forStore($activeStoreId)->whereDate('created_at', $today)->sum('total_price');
        $todayProfit = Transaction::forStore($activeStoreId)->whereDate('created_at', $today)->sum('total_profit');
        
        $todayExpenses = Expense::where('store_id', $activeStoreId)->whereDate('created_at', $today)->sum('amount');
        $todayNetProfit = $todayProfit - $todayExpenses;

        // 2. STATISTIK BULAN INI (FILTER BY STORE_ID)
        $monthSales = Transaction::forStore($activeStoreId)->whereDate('created_at', '>=', $startOfMonth)->sum('total_price');
        $monthProfit = Transaction::forStore($activeStoreId)->whereDate('created_at', '>=', $startOfMonth)->sum('total_profit');

        // 3. SHIFT SAAT INI / SHIFT TERAKHIR (FILTER BY STORE_ID)
        $activeShift = Shift::getActiveShift($activeStoreId);
        $recentShifts = Shift::where('store_id', $activeStoreId)->with('user')->latest()->take(5)->get();

        // 4. TRANSAKSI TERBARU (FILTER BY STORE_ID)
        $latestTransactions = Transaction::forStore($activeStoreId)->with(['user', 'details.product'])->latest()->take(5)->get();

        // 5. DATA GRAFIK PENJUALAN & PROFIT 6 BULAN TERAKHIR (FILTER BY STORE_ID)
        $monthlyChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;

            $sales = Transaction::forStore($activeStoreId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_price');

            $profit = Transaction::forStore($activeStoreId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_profit');

            $monthlyChartData[] = [
                'month_name' => $date->format('M Y'),
                'sales'      => (float) $sales,
                'profit'     => (float) $profit,
            ];
        }

        return view('dashboard.index', compact(
            'activeStoreId', // <--- Kirim ke View
            'todaySales',
            'todayProfit',
            'todayExpenses',
            'todayNetProfit',
            'monthSales',
            'monthProfit',
            'activeShift',
            'recentShifts',
            'latestTransactions',
            'monthlyChartData'
        ));
    }

    /**
     * Menampilkan Halaman Monitoring Absensi Karyawan & Audit Kas Toko (Khusus Owner)
     */
    public function monitoring()
    {
        $storeId = $this->getActiveStoreId();

        $activeShift = Shift::where('store_id', $storeId)->where('status', 'open')->with(['user', 'expenses'])->first();

        $currentCashSales = 0;
        $currentExpenses  = 0;

        if ($activeShift) {
            $currentExpenses = $activeShift->expenses->sum('amount');
            $currentCashSales = Transaction::forStore($storeId)
                ->where('shift_id', $activeShift->id)
                ->where('payment_method', 'cash')
                ->sum('total_price');
        }

        $shifts = Shift::where('store_id', $storeId)->with(['user', 'expenses'])->latest()->get();

        $startOfMonth = Carbon::now()->startOfMonth();
        
        $totalSelisihBulanIni = Shift::where('store_id', $storeId)
            ->where('status', 'closed')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->get()
            ->sum(function ($shift) {
                $expected = ($shift->start_cash ?? 0) + ($shift->cash_sales ?? 0) - ($shift->total_expenses ?? 0);
                return ($shift->end_cash ?? 0) - $expected;
            });

        $expenses = Expense::where('store_id', $storeId)->with('user')->latest()->get();

        return view('owner.monitoring', compact(
            'activeShift',
            'currentCashSales',
            'currentExpenses',
            'shifts',
            'totalSelisihBulanIni',
            'expenses'
        ));
    }
}