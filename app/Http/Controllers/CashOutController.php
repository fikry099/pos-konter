<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashOutController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Halaman Form Tarik Tunai & Riwayat Tarik Tunai Hari Ini
     */
    public function index()
    {
        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        $store = \App\Models\Store::find($storeId);
        $storeName = $store ? $store->name : 'CABANG';

        $todayCashOuts = Transaction::where('store_id', $storeId)
            ->whereHas('details', function ($q) {
                $q->where('custom_name', 'like', 'Tarik Tunai%');
            })
            ->with(['details', 'user'])
            ->latest()
            ->take(15)
            ->get();

        return view('cash-outs.index', compact('activeShift', 'todayCashOuts', 'storeName'));
    }

    /**
     * Memproses Transaksi Tarik Tunai
     */
    public function store(Request $request)
    {
        $request->validate([
            'cash_amount'          => 'required|numeric|min:1000',
            'admin_fee'            => 'required|numeric|min:0',
            'admin_payment_method' => 'required|in:transfer,cash',
            'target_bank'          => 'required|string',
            'payment_proof'        => 'required|string',
        ], [
            'cash_amount.min'        => 'Nominal tarik tunai minimal Rp 1.000',
            'payment_proof.required' => 'Foto bukti transfer ke rekening cabang wajib diambil!',
        ]);

        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('error', 'Shift tidak aktif! Silakan buka shift terlebih dahulu.');
        }

        $cashAmount = (float) $request->cash_amount;
        $adminFee   = (float) $request->admin_fee;
        $adminMethod = $request->admin_payment_method; // 'transfer' atau 'cash'

        // Hitung total transfer masuk berdasarkan pilihan bayar admin
        $totalTransfer = ($adminMethod === 'transfer') ? ($cashAmount + $adminFee) : $cashAmount;

        $bankName = strtoupper(trim($request->target_bank));

        $firstCategory = \App\Models\Category::first();
        $categoryId = $firstCategory ? $firstCategory->id : 1;

        $dummyProduct = Product::firstOrCreate(
            ['code' => 'TARIK-TUNAI'],
            [
                'category_id'   => $categoryId,
                'name'          => 'Layanan Tarik Tunai',
                'type'          => 'digital',
                'cost_price'    => 0,
                'selling_price' => 0,
                'stock'         => 999999,
                'is_active'     => true,
            ]
        );

        $adminTag = ($adminMethod === 'cash') ? ' (Admin Tunai)' : ' (Admin Transfer)';
        $customName = 'Tarik Tunai ' . $bankName . ' - Rp ' . number_format($cashAmount, 0, ',', '.') . $adminTag;

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'store_id'       => $storeId,
                'invoice_code'   => 'WD-' . date('YmdHis') . '-' . rand(100, 999),
                'user_id'        => Auth::id() ?? $activeShift->user_id,
                'shift_id'       => $activeShift->id,
                'total_cost'     => 0,
                'total_price'    => $adminFee,
                'total_profit'   => $adminFee,
                'pay_amount'     => $totalTransfer,
                'change_amount'  => 0,
                'payment_method' => 'qris',
                'payment_proof'  => $request->payment_proof,
            ]);

            TransactionDetail::create([
                'transaction_id'    => $transaction->id,
                'product_id'        => $dummyProduct->id,
                'custom_name'       => $customName,
                'served_by_user_id' => Auth::id(),
                'target_phone'      => null,
                'digital_provider'  => $bankName,
                'qty'               => 1,
                'cost_price'        => 0,
                'selling_price'     => $adminFee,
                'subtotal'          => $adminFee,
                'profit'            => $adminFee,
            ]);

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaksi Tarik Tunai sebesar Rp ' . number_format($cashAmount, 0, ',', '.') . ' berhasil diproses!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses Tarik Tunai: ' . $e->getMessage());
        }
    }
}