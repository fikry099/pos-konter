<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Helper privat untuk menentukan store_id cabang yang aktif
     */
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Helper privat untuk menerapkan seluruh filter transaksi (Reuseable)
     */
    private function applyTransactionFilters($query, Request $request)
    {
        // Filter 1: Search Nota / No HP Target
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhereHas('details', function ($qd) use ($search) {
                      $qd->where('target_phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter 2: Tanggal Transaksi
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter 3: Shift Kerja
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Filter 4: Kategori Produk / Katalog (Optimasi Query via Pluck ID)
        if ($request->filled('category_id')) {
            $catId = $request->category_id;

            $categoryIds = Category::where('id', $catId)
                ->orWhere('parent_id', $catId)
                ->orWhereHas('parent', function ($qp) use ($catId) {
                    $qp->where('parent_id', $catId);
                })
                ->pluck('id');

            $query->whereHas('details.product', function ($qp) use ($categoryIds) {
                $qp->whereIn('category_id', $categoryIds);
            });
        }

        return $query;
    }

    /**
     * Halaman Utama Riwayat Transaksi Penjualan
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $categories = Category::whereNull('parent_id')->get();
        
        // Filter shift khusus cabang aktif
        $shifts = Shift::where('store_id', $storeId)->latest()->get();

        // Kueri Dasar Filter
        $baseQuery = Transaction::forStore($storeId);
        $this->applyTransactionFilters($baseQuery, $request);

        // Calculate Summary secara cepat langsung dari database
        $summary = [
            'total_omset'  => (clone $baseQuery)->sum('total_price'),
            'total_profit' => (clone $baseQuery)->sum('total_profit'),
        ];

        // Eager load relasi HANYA saat mengambil data transaksi paginasi
        $transactions = $baseQuery
            ->with(['user', 'shift', 'details.product.category', 'details.servedBy'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

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

        $this->applyTransactionFilters($query, $request);

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

    /**
     * API Detail Transaksi (Modal Pop-Up Detail)
     */
    public function show($id)
    {
        $storeId = $this->getActiveStoreId();
        $transaction = Transaction::forStore($storeId)
            ->with(['details.product', 'details.servedBy', 'user', 'shift'])
            ->findOrFail($id);

        return response()->json($transaction);
    }
}