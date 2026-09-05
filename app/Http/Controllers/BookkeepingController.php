<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Expense;
use App\Models\Store;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookkeepingController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
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

            // Data Absensi (Lepas filter store_id agar karyawan store_id = null tetap terbaca)
            $attendancesQuery = Attendance::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month);

            $totalOnTime    = (clone $attendancesQuery)->where('is_on_time', true)->count();
            $totalLate      = (clone $attendancesQuery)->where('is_on_time', false)->count();
            $attendanceList = (clone $attendancesQuery)->latest('date')->latest('check_in')->get();

            // Total Aksesoris Terjual
            $accessoryQuery = TransactionDetail::whereHas('transaction', function ($q) use ($storeId, $year, $month) {
                    $q->where('store_id', $storeId)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
                })
                ->where('served_by_user_id', $employee->id)
                ->whereHas('product', function ($p) {
                    $p->where('type', '!=', 'digital');
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

        // Pembukuan Keuangan
        $trxQuery = Transaction::forStore($storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $totalOmset     = (float) (clone $trxQuery)->sum('total_price');
        $totalCost      = (float) (clone $trxQuery)->sum('total_cost');
        $grossProfit    = (float) (clone $trxQuery)->sum('total_profit');

        $totalExpenses  = (float) Expense::forStore($storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('amount');

        $totalBonusAllocation = $employeeReport->sum('total_bonus');
        $netProfit            = $grossProfit - $totalExpenses - $totalBonusAllocation;

        $financialSummary = [
            'total_omset'            => $totalOmset,
            'total_cost'             => $totalCost,
            'gross_profit'           => $grossProfit,
            'total_expenses'         => $totalExpenses,
            'total_bonus_allocation' => $totalBonusAllocation,
            'net_profit'             => $netProfit,
        ];

        return view('owner.bookkeeping.index', compact(
            'selectedStore', 'month', 'year', 'employeeReport', 'financialSummary'
        ));
    }

    /**
     * Halaman Detail Riwayat Absensi Karyawan
     */
    public function userAttendanceDetail(Request $request, User $user)
    {
        $month = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
        $year  = $request->input('year', date('Y'));

        $startDate = "{$year}-{$month}-01";
        $endDate   = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();

        // Query absensi murni berdasarkan User ID
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