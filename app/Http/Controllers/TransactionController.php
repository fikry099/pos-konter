<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Halaman Utama Riwayat Transaksi Penjualan
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $categories = Category::whereNull('parent_id')->get();
        $shifts = Shift::where('store_id', $storeId)->latest()->get();

        // Eager load details.servedBy untuk mengambil nama karyawan penanggung jawab aksesoris
        $query = Transaction::forStore($storeId)
            ->with(['user', 'shift', 'details.product.category', 'details.servedBy']);

        // Filter 1: Search Nota / No HP
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhereHas('details', function ($qd) use ($search) {
                      $qd->where('target_phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter 2: Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter 3: Shift Kerja
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Filter 4: Kategori Produk / Katalog
        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            $query->whereHas('details.product.category', function ($qc) use ($catId) {
                $qc->where('id', $catId)
                   ->orWhere('parent_id', $catId)
                   ->orWhereHas('parent', function ($qparent) use ($catId) {
                       $qparent->where('parent_id', $catId);
                   });
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $summary = [
            'total_omset'  => (clone $query)->sum('total_price'),
            'total_profit' => (clone $query)->sum('total_profit'),
        ];

        return view('transactions.index', compact('transactions', 'categories', 'shifts', 'summary'));
    }

    /**
     * Export Excel dengan Filter Kategori & Penanggung Jawab
     */
    public function exportExcel(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $query = Transaction::forStore($storeId)
            ->with(['user', 'shift', 'details.product.category', 'details.servedBy']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhereHas('details', function ($qd) use ($search) {
                      $qd->where('target_phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            $query->whereHas('details.product.category', function ($qc) use ($catId) {
                $qc->where('id', $catId)
                   ->orWhere('parent_id', $catId)
                   ->orWhereHas('parent', function ($qparent) use ($catId) {
                       $qparent->where('parent_id', $catId);
                   });
            });
        }

        $transactions = $query->latest()->get();

        $totalOmset  = $transactions->sum('total_price');
        $totalCost   = $transactions->sum('total_cost');
        $totalProfit = $transactions->sum('total_profit');

        $html = view('transactions.export-excel', compact('transactions', 'totalOmset', 'totalCost', 'totalProfit'))->render();

        $fileName = 'Laporan_Transaksi_' . date('Ymd_His') . '.xls';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function show($id)
    {
        $storeId = $this->getActiveStoreId();
        $transaction = Transaction::forStore($storeId)
            ->with(['details.product', 'details.servedBy', 'user', 'shift'])
            ->findOrFail($id);

        return response()->json($transaction);
    }
}