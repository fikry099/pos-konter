<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Filter 4: Kategori Produk / Katalog
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
        $shifts = Shift::where('store_id', $storeId)->latest()->get();

        // Kueri Dasar Filter Cabang
        $baseQuery = Transaction::forStore($storeId);

        // =========================================================================
        // ATURAN 1: BILA KARYAWAN -> SEMBUNYIKAN TRANSAKSI YANG SUDAH DIBATALKAN
        // (Bila Owner -> Tampilkan Semua Transaksi Sukses & Batal untuk Audit)
        // =========================================================================
        if (Auth::user()->role !== 'owner') {
            $baseQuery->completed();
        }

        $this->applyTransactionFilters($baseQuery, $request);

        // Omset & Profit HANYA menghitung transaksi yang BERHASIL (Completed)
        $summaryQuery = (clone $baseQuery)->completed();
        $summary = [
            'total_omset'  => $summaryQuery->sum('total_price'),
            'total_profit' => $summaryQuery->sum('total_profit'),
        ];

        // Ambil data transaksi
        $transactions = $baseQuery
            ->with(['user', 'shift', 'details.product.category', 'details.servedBy', 'cancelledBy'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions', 'categories', 'shifts', 'summary'));
    }

    /**
     * Process Pembatalan Transaksi (Void & Restock)
     */
    public function cancel(Request $request, $id)
    {
        // =========================================================================
        // ATURAN 2: PROTEKSI SERVER - OWNER TIDAK BISA MEMBATALKAN TRANSAKSI
        // =========================================================================
        if (Auth::user()->role === 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Owner hanya bertugas memantau/mengaudit dan tidak dapat membatalkan transaksi.'
            ], 403);
        }

        $request->validate([
            'cancel_reason' => 'required|string|min:3|max:255',
        ], [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi!',
            'cancel_reason.min'      => 'Alasan pembatalan minimal 3 karakter.'
        ]);

        $storeId = $this->getActiveStoreId();

        DB::beginTransaction();
        try {
            $transaction = Transaction::forStore($storeId)
                ->with('details.product')
                ->findOrFail($id);

            if ($transaction->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ini sudah dibatalkan sebelumnya.'
                ], 400);
            }

            // 1. KEMBALIKAN STOK BARANG PENJUALAN
            foreach ($transaction->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stock', $detail->qty);
                }
            }

            // 2. UPDATE STATUS TRANSAKSI TANDAI BANYAK METADATA BATAL
            $transaction->update([
                'status'        => 'cancelled',
                'cancel_reason' => $request->cancel_reason,
                'cancelled_by'  => Auth::id(),
                'cancelled_at'  => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan dan stok produk telah dikembalikan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Excel dengan Filter
     */
    public function exportExcel(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $query = Transaction::forStore($storeId)
            ->completed() // Hanya ekspor transaksi yang sukses
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
            ->with(['details.product', 'details.servedBy', 'user', 'shift', 'cancelledBy'])
            ->findOrFail($id);

        return response()->json($transaction);
    }
}