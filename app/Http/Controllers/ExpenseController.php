<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Shift;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Helper privat untuk mengambil store_id aktif
     */
    private function getActiveStoreId()
    {
        return auth()->user()->store_id ?? session('selected_store_id');
    }

    /**
     * Menampilkan Daftar Pengeluaran Shift Aktif & Riwayat Pengeluaran per Cabang
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        // Ambil shift aktif khusus cabang ini
        $activeShift = Shift::getActiveShift($storeId);
        
        // Filter pengeluaran sesuai cabang menggunakan scope forStore
        $query = Expense::forStore($storeId)->with(['user', 'shift']);

        // Filter berdasarkan Shift tertentu (jika dipilih)
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $expenses  = $query->latest()->paginate(15);
        $allShifts = Shift::where('store_id', $storeId)->with('user')->latest()->take(30)->get();

        // Hitung total pengeluaran khusus pada shift yang sedang berjalan
        $activeShiftExpensesTotal = $activeShift ? $activeShift->expenses()->sum('amount') : 0;

        return view('expenses.index', compact('expenses', 'activeShift', 'allShifts', 'activeShiftExpensesTotal'));
    }

    /**
     * Halaman Pengeluaran Khusus Owner (Dengan Filter Bulan & Tahun)
     */
    public function ownerIndex(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $month = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
        $year  = $request->input('year', date('Y'));

        $query = Expense::forStore($storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with(['user', 'shift']);

        $expenses = $query->latest()->paginate(15)->withQueryString();
        $totalExpenses = (clone $query)->sum('amount');

        return view('owner.expenses.index', compact('expenses', 'totalExpenses', 'month', 'year'));
    }

    /**
     * Menyimpan Catatan Pengeluaran Kas Baru (Terikat Shift Aktif & Cabang)
     */
    public function store(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        // Ambil shift aktif spesifik cabang
        $activeShift = Shift::getActiveShift($storeId);

        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('error', 'Akses ditolak! Anda harus membuka shift terlebih dahulu sebelum mencatat pengeluaran.');
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
        ]);

        Expense::create([
            'store_id'    => $storeId, // Injeksi store_id otomatis
            'shift_id'    => $activeShift->id,
            'user_id'     => auth()->id(),
            'description' => $request->description,
            'amount'      => $request->amount,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran kas toko berhasil dicatat!');
    }

    /**
     * Menghapus Catatan Pengeluaran
     */
    public function destroy($id)
    {
        $storeId = $this->getActiveStoreId();

        // Kunci proteksi agar user cabang tidak bisa menghapus pengeluaran milik cabang lain
        $expense = Expense::forStore($storeId)->findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    /**
     * Menyimpan Pengeluaran Baru Langsung dari Panel Owner
     */
    public function ownerStore(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
        ]);

        Expense::create([
            'store_id'    => $storeId,
            'shift_id'    => null, // Tidak terikat shift karena dicatat langsung oleh owner
            'user_id'     => auth()->id(),
            'description' => $request->description,
            'amount'      => $request->amount,
        ]);

        return redirect()->route('owner.expenses.index')->with('success', 'Pengeluaran atau pembayaran restok berhasil dicatat!');
    }
}