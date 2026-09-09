<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Helper privat untuk mengambil seluruh ID Kategori
     */
    private function getAllCategoryIds($catId)
    {
        $ids = [(int)$catId];
        
        // Level 2 (Sub-Kategori)
        $childrenIds = Category::where('parent_id', $catId)->pluck('id')->toArray();
        if (!empty($childrenIds)) {
            $ids = array_merge($ids, $childrenIds);
            
            // Level 3 (Kategori Spesifik/Provider)
            $grandChildrenIds = Category::whereIn('parent_id', $childrenIds)->pluck('id')->toArray();
            if (!empty($grandChildrenIds)) {
                $ids = array_merge($ids, $grandChildrenIds);
            }
        }

        return array_unique(array_map('intval', $ids));
    }

    /**
     * API Endpoint untuk mengambil Sub-Kategori via AJAX
     */
    public function getSubCategories($parentId)
    {
        $subCategories = Category::where('parent_id', $parentId)->get(['id', 'name']);
        return response()->json($subCategories);
    }

    /**
     * Helper privat untuk menerapkan seluruh filter transaksi
     */
    private function applyTransactionFilters($query, Request $request)
    {
        // 1. Filter Search (Nota / No HP Target / Nama Item Kustom)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhereHas('details', function ($qd) use ($search) {
                      $qd->where('target_phone', 'like', "%{$search}%")
                        ->orWhere('custom_name', 'like', "%{$search}%")
                        ->orWhereHas('product', function($qp) use ($search) {
                            $qp->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        // 2. Filter Tanggal Transaksi
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 3. Filter Shift Kerja
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // 4. Filter Metode Pembayaran
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // 5. Filter Kategori ID & Sub-Kategori ID
        $targetCatId = $request->filled('sub_category_id') ? $request->sub_category_id : $request->category_id;

        if ($targetCatId) {
            $allCategoryIds = $this->getAllCategoryIds($targetCatId);
            $selectedCategory = Category::find($targetCatId);
            $catName = strtolower($selectedCategory->name ?? '');

            // JIKA KATEGORI ADALAH PULSA REGULER
            if (str_contains($catName, 'pulsa')) {
                $query->where('invoice_code', 'NOT LIKE', 'WD-%');

                $query->whereHas('details.product', function ($qp) use ($allCategoryIds) {
                    $qp->whereIn('category_id', $allCategoryIds);
                });

                $query->whereDoesntHave('details', function ($qd) {
                    $qd->where(function($qKw) {
                        $qKw->whereRaw('LOWER(custom_name) LIKE ?', ['%top-up%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%topup%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%dana%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%gopay%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%ovo%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%shopee%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%linkaja%'])
                            ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%transfer%']);
                    });
                });
            } 
            // JIKA KATEGORI ADALAH E-WALLET / TOP-UP
            elseif (str_contains($catName, 'wallet') || str_contains($catName, 'top-up') || str_contains($catName, 'topup')) {
                $query->whereHas('details', function ($qd) use ($allCategoryIds) {
                    $qd->where(function ($qSub) use ($allCategoryIds) {
                        $qSub->whereHas('product', function ($qp) use ($allCategoryIds) {
                            $qp->whereIn('category_id', $allCategoryIds);
                        })
                        ->orWhereNull('product_id')
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%top-up%'])
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%topup%'])
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%dana%'])
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%gopay%'])
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%ovo%'])
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%shopee%'])
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%linkaja%']);
                    });
                });
            }
            // JIKA KATEGORI ADALAH BANK / TRANSFER
            elseif (str_contains($catName, 'bank') || str_contains($catName, 'transfer')) {
                $query->whereHas('details', function ($qd) use ($allCategoryIds) {
                    $qd->where(function ($qSub) use ($allCategoryIds) {
                        $qSub->whereHas('product', function ($qp) use ($allCategoryIds) {
                            $qp->whereIn('category_id', $allCategoryIds);
                        })
                        ->orWhereRaw('LOWER(custom_name) LIKE ?', ['%transfer%'])
                        ->orWhere('invoice_code', 'LIKE', 'WD-%');
                    });
                });
            }
            // KATEGORI LAINNYA (Aksesoris, HP, Provider Spesifik, dll)
            else {
                $query->whereHas('details.product', function ($qp) use ($allCategoryIds) {
                    $qp->whereIn('category_id', $allCategoryIds);
                });
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $categories = Category::whereNull('parent_id')->get();
        $shifts     = Shift::where('store_id', $storeId)->latest()->get();

        $baseQuery = Transaction::forStore($storeId);

        if (Auth::user()->role !== 'owner') {
            $baseQuery->completed();
        }

        $this->applyTransactionFilters($baseQuery, $request);

        $rawTransactions = $baseQuery
            ->with(['user', 'shift', 'details.product.category', 'details.servedBy', 'cancelledBy'])
            ->latest()
            ->get();

        // Ringkasan
        $summary = [
            'total_omset'  => $rawTransactions->sum('total_price'),
            'total_profit' => $rawTransactions->sum('total_profit'),
        ];

        // Pagination
        $page = request()->get('page', 1);
        $perPage = 15;
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $rawTransactions->forPage($page, $perPage)->values(),
            $rawTransactions->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('transactions.index', compact('transactions', 'categories', 'shifts', 'summary'));
    }

    public function cancel(Request $request, $id)
    {
        if (Auth::user()->role === 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Owner hanya bertugas memantau/mengaudit dan tidak dapat membatalkan transaksi.'
            ], 403);
        }

        $request->validate([
            'cancel_reason' => 'required|string|min:3|max:255',
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

            foreach ($transaction->details as $detail) {
                if ($detail->product && $detail->product->type === 'physical') {
                    $detail->product->increment('stock', $detail->qty);
                }
            }

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
     * Ekspor Laporan Transaksi ke File Excel (.xlsx Murni)
     * Kompatibel penuh dengan Android, iOS, WPS Office, MS Excel & Google Sheets
     */
    public function exportExcel(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $query = Transaction::forStore($storeId)
            ->completed()
            ->with(['user', 'shift', 'details.product.category', 'details.servedBy']);

        // Terapkan seluruh filter persis seperti di tampilan halaman
        $this->applyTransactionFilters($query, $request);

        $transactions = $query->latest()->get();

        $totalOmset  = $transactions->sum('total_price');
        $totalCost   = $transactions->sum('total_cost');
        $totalProfit = $transactions->sum('total_profit');

        $fileName = 'Laporan_Transaksi_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new TransactionHistoryExport($transactions, $totalOmset, $totalCost, $totalProfit), 
            $fileName
        );
    }

    public function show($id)
    {
        $storeId = $this->getActiveStoreId();
        $transaction = Transaction::forStore($storeId)
            ->with(['details.product', 'details.servedBy', 'user', 'shift', 'cancelledBy'])
            ->findOrFail($id);

        return response()->json($transaction);
    }
}