<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    private function getActiveStoreId()
    {
        $user = Auth::user();
        if ($user && $user->store_id) {
            return $user->store_id;
        }
        return session('selected_store_id') ?? 1;
    }

    public function index()
    {
        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        $expectedCash = 0;
        if ($activeShift) {
            // Hitung total penjualan khusus TUNAI (cash) pada shift ini
            $totalCashSales = Transaction::where('shift_id', $activeShift->id)
                ->where('payment_method', 'cash')
                ->sum('total_price');

            // Hitung pengeluaran pada shift ini
            $totalExpenses = Expense::where('shift_id', $activeShift->id)->sum('amount');

            // Kalkulasi Ekspektasi Kas di Laci
            $expectedCash = ($activeShift->cash_initial + $totalCashSales) - $totalExpenses;
        }

        // Ambil ID Karyawan yang SUDAH ABSEN HARI INI DI CABANG MANAPUN
        $alreadyClockedInTodayUserIds = Attendance::whereDate('date', now()->toDateString())
            ->pluck('user_id')
            ->toArray();

        // Ambil SELURUH Karyawan dan tandai apakah sudah absen hari ini
        $users = User::where('role', 'karyawan')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($user) use ($alreadyClockedInTodayUserIds) {
                $user->has_clocked_in_today = in_array($user->id, $alreadyClockedInTodayUserIds);
                return $user;
            });

        // Optimasi N+1 Query dengan eager loading relasi user
        $shifts = Shift::with(['user'])
            ->where('store_id', $storeId)
            ->latest()
            ->paginate(10);

        return view('shifts.index', compact('activeShift', 'users', 'shifts', 'expectedCash'));
    }

    /**
     * Membuka Shift Baru ATAU Karyawan Menyusul Masuk Shift Aktif
     */
    public function store(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $existingActive = Shift::getActiveShift($storeId);

        // SYARAT VALIDASI BERDASARKAN APAKAH SUDAH ADA SHIFT AKTIF
        if (!$existingActive) {
            $request->validate([
                'user_id'      => 'required|exists:users,id',
                'cash_initial' => 'required|numeric|min:0',
                'photo'        => 'required',
            ]);
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'photo'   => 'required',
            ]);
        }

        $now = now();
        $currentTime = $now->format('H:i:s');
        $currentDate = $now->toDateString();

        // DOUBLE CHECK SECURITY: Cek apakah karyawan ini sudah absen hari ini
        $hasClockedInToday = Attendance::where('user_id', $request->user_id)
            ->whereDate('date', $currentDate)
            ->exists();

        if ($hasClockedInToday) {
            $user = User::find($request->user_id);
            $userName = $user ? $user->name : 'Karyawan';
            return redirect()->back()->with('error', "Gagal! {$userName} sudah melakukan absensi shift hari ini di salah satu cabang.");
        }

        // Tentukan Jenis Shift & Toleransi Keterlambatan Per Individu
        $hour = (int) $now->format('H');
        $shiftType = ($hour < 15) ? 'pagi' : 'sore';
        $threshold = ($shiftType === 'pagi') ? '07:05:00' : '15:05:00';
        $isOnTime = ($currentTime <= $threshold);

        DB::beginTransaction();
        try {
            if (!$existingActive) {
                // KONDISI A: BUKA SHIFT BARU (ORANG PERTAMA)
                $shift = Shift::create([
                    'store_id'     => $storeId,
                    'user_id'      => $request->user_id,
                    'user_ids'     => [(int) $request->user_id],
                    'cash_initial' => $request->cash_initial,
                    'photo'        => $request->photo,
                    'start_time'   => $now,
                    'status'       => 'open',
                ]);

                // Catat Absensi Pembuka Shift
                Attendance::create([
                    'store_id'   => $storeId,
                    'shift_id'   => $shift->id,
                    'user_id'    => $request->user_id,
                    'date'       => $currentDate,
                    'shift_type' => $shiftType,
                    'check_in'   => $currentTime,
                    'is_on_time' => $isOnTime,
                ]);

                DB::commit();
                return redirect()->route('pos.index')->with('success', 'Shift berhasil dibuka dan absensi kasir pertama dicatat!');
            } else {
                // KONDISI B: KARYAWAN MENYUSUL (JOIN SHIFT)
                $currentUserIds = $existingActive->user_ids ?? [];
                if (!in_array($request->user_id, $currentUserIds)) {
                    $currentUserIds[] = (int) $request->user_id;
                }

                $existingActive->update([
                    'user_ids' => $currentUserIds,
                ]);

                // Catat Absensi Karyawan Susulan
                Attendance::create([
                    'store_id'   => $storeId,
                    'shift_id'   => $existingActive->id,
                    'user_id'    => $request->user_id,
                    'date'       => $currentDate,
                    'shift_type' => $shiftType,
                    'check_in'   => $currentTime,
                    'is_on_time' => $isOnTime,
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'Absensi karyawan susulan berhasil dicatat ke shift aktif!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses shift/absensi: ' . $e->getMessage());
        }
    }

    public function closeShift(Request $request, $id)
    {
        $request->validate([
            'cash_actual' => 'required|numeric|min:0',
        ]);

        $shift = Shift::findOrFail($id);

        // PERBAIKAN BUG: Hitung Penjualan Khusus TUNAI (cash) Agar Akurat dengan Laci Kas
        $totalCashSales = Transaction::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $totalExpenses = Expense::where('shift_id', $shift->id)->sum('amount');
        
        $expectedCash = ($shift->cash_initial + $totalCashSales) - $totalExpenses;
        $difference = $request->cash_actual - $expectedCash;

        $shift->update([
            'end_time'      => now(),
            'cash_actual'   => $request->cash_actual,
            'cash_expected' => $expectedCash,
            'difference'    => $difference,
            'status'        => 'closed',
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditutup dan ringkasan pembukuan telah disimpan!');
    }
}