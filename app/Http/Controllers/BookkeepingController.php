<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Expense;
use App\Models\Store;
use App\Models\Attendance;
use App\Models\Restock;
use App\Exports\BookkeepingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookkeepingController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Helper privat untuk mengambil data rekap keuangan dan bonus karyawan
     */
    private function getBookkeepingData($storeId, $month, $year)
    {
        $bonusRate = 1000;

        // 1. Ambil HANYA user role KARYAWAN
        $employees = User::where('role', 'karyawan')->get();

        $employeeReport = $employees->map(function ($employee) use ($storeId, $month, $year, $bonusRate) {
            // Total Shift Ditugaskan
            $totalShifts = Shift::where('store_id', $storeId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get()
                ->filter(function ($shift) use ($employee) {
                    if (is_array($shift->user_ids)) {
                        return in_array($employee->id, $shift->user_ids);
                    }
                    return $shift->user_id == $employee->id;
                })->count();

            // Data Absensi
            $attendancesQuery = Attendance::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month);

            $totalOnTime    = (clone $attendancesQuery)->where('is_on_time', true)->count();
            $totalLate      = (clone $attendancesQuery)->where('is_on_time', false)->count();
            $attendanceList = (clone $attendancesQuery)->latest('date')->latest('check_in')->get();

            // Total Aksesoris Terjual (KHUSUS KATEGORI AKSESORIS)
            $accessoryQuery = TransactionDetail::whereHas('transaction', function ($q) use ($storeId, $year, $month) {
                    $q->where('store_id', $storeId)
                      ->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
                })
                ->where('served_by_user_id', $employee->id)
                ->whereHas('product.category', function ($c) {
                    $c->where(function ($q) {
                        $q->where('slug', 'LIKE', '%aksesoris%')
                          ->orWhere('name', 'LIKE', '%Aksesoris%')
                          ->orWhereHas('parent', function ($parentQuery) {
                              $parentQuery->where('slug', 'LIKE', '%aksesoris%')
                                          ->orWhere('name', 'LIKE', '%Aksesoris%')
                                          ->orWhereHas('parent', function ($grandParentQuery) {
                                              $grandParentQuery->where('slug', 'LIKE', '%aksesoris%')
                                                               ->orWhere('name', 'LIKE', '%Aksesoris%');
                                          });
                          });
                    });
                });

            $totalAccessoriesQty   = (int) $accessoryQuery->sum('qty');
            $totalAccessoriesOmset = (float) $accessoryQuery->sum('subtotal');
            $totalBonusAmount      = $totalAccessoriesQty * $bonusRate;

            return [
                'user_id'           => $employee->id,
                'name'              => $employee->name,
                'role'              => $employee->role,
                'total_shifts'      => $totalShifts,
                'total_ontime'      => $totalOnTime,
                'total_late'        => $totalLate,
                'attendances'       => $attendanceList,
                'accessories_qty'   => $totalAccessoriesQty,
                'accessories_omset' => $totalAccessoriesOmset,
                'bonus_rate'        => $bonusRate,
                'total_bonus'       => $totalBonusAmount,
            ];
        });

        // 2. Pembukuan Keuangan
        $trxQuery = Transaction::forStore($storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

            $transactions = (clone $trxQuery)->get();

            $totalOmset     = (float) $transactions->sum('total_price');
            $totalCost      = (float) $transactions->sum('total_cost');
            $grossProfit    = (float) $transactions->sum('total_profit');
    
            // Hitung Uang Tunai Penjualan
            $totalCash = (float) $transactions->filter(function($trx) {
                $method = strtolower($trx->payment_method ?? '');
                return in_array($method, ['cash', 'tunai', 'cash/tunai', '']);
            })->sum('total_price');
    
            // Hitung QRIS & Non-Tunai
            $totalQris = (float) $transactions->filter(function($trx) {
                $method = strtolower($trx->payment_method ?? '');
                return in_array($method, ['qris', 'transfer', 'bank', 'ewallet', 'non-cash']);
            })->sum(function($trx) {
                if (str_starts_with($trx->invoice_code ?? '', 'WD-')) {
                    return (float) ($trx->pay_amount ?? 0);
                }
                return (float) $trx->total_price;
            });
    
            $totalExpenses  = (float) Expense::forStore($storeId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('category', 'operational')
                ->sum('amount');
    
            $totalBonusAllocation = $employeeReport->sum('total_bonus');
            
            // PERUBAHAN LOGIKA: Beban operasional TIDAK LAGI mengurangi laba bersih
            // Laba bersih sekarang hanya Gross Profit dikurangi Alokasi Bonus Karyawan
            $netProfit = $grossProfit - $totalBonusAllocation;
    
            $financialSummary = [
                'total_omset'            => $totalOmset,
                'total_cost'             => $totalCost,
                'gross_profit'           => $grossProfit,
                'total_expenses'         => $totalExpenses,
                'total_bonus_allocation' => $totalBonusAllocation,
                'net_profit'             => $netProfit,
                'total_cash'             => $totalCash,
                'total_qris'             => $totalQris,
            ];

        return [
            'employeeReport'   => $employeeReport,
            'financialSummary' => $financialSummary,
        ];
    }

    /**
     * Halaman Utama Pembukuan & Rekap Bonus Karyawan (Role Owner)
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $selectedStore = Store::find($storeId);

        $month = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
        $year  = $request->input('year', date('Y'));

        $data = $this->getBookkeepingData($storeId, $month, $year);

        $totalRestockCost = Restock::where('store_id', $storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total_cost');

        return view('owner.bookkeeping.index', [
            'selectedStore'    => $selectedStore,
            'month'            => $month,
            'year'             => $year,
            'employeeReport'   => $data['employeeReport'],
            'financialSummary' => $data['financialSummary'],
            'totalRestockCost' => $totalRestockCost
        ]);
    }

    /**
     * Export Laporan Pembukuan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $month   = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
        $year    = $request->input('year', date('Y'));

        $data = $this->getBookkeepingData($storeId, $month, $year);

        $fileName = 'Pembukuan_Laporan_' . date('F', mktime(0, 0, 0, (int)$month, 1)) . "_{$year}.xlsx";

        return Excel::download(
            new BookkeepingExport(
                $data['financialSummary'], 
                $data['employeeReport'], 
                $month, 
                $year
            ), 
            $fileName
        );
    }

    public function restockHistory(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $selectedStore = Store::find($storeId);

        $month = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
        $year  = $request->input('year', date('Y'));

        $query = Restock::where('store_id', $storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with(['product', 'user']);

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->whereHas('product', function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(code) LIKE ?', ["%{$search}%"]);
            });
        }

        $totalRestockCost = (clone $query)->sum('total_cost');
        $restockHistories = $query->latest()->paginate(15)->withQueryString();

        return view('owner.bookkeeping.restock-history', compact(
            'selectedStore',
            'month',
            'year',
            'restockHistories',
            'totalRestockCost'
        ));
    }

    public function userAttendanceDetail(Request $request, User $user)
    {
        $month = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
        $year  = $request->input('year', date('Y'));

        $startDate = "{$year}-{$month}-01";
        $endDate   = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();

        $baseQuery = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);

        $totalOnTime = (clone $baseQuery)->where('is_on_time', true)->count();
        $totalLate   = (clone $baseQuery)->where('is_on_time', false)->count();

        $attendances = (clone $baseQuery)
            ->latest('date')
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();

        return view('owner.bookkeeping.user-detail', compact(
            'user', 'attendances', 'month', 'year', 'totalOnTime', 'totalLate'
        ));
    }
}