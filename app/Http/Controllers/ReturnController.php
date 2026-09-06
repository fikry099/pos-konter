<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\ReturnDetail;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\StoreProductStock;
use App\Models\Shift;
use App\Models\Category;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    /**
     * Halaman Utama & Riwayat Retur Barang
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();

        $returns = ProductReturn::forStore($storeId)
            ->with(['transaction.user', 'user', 'details.product'])
            ->latest()
            ->paginate(15);

        return view('returns.index', compact('returns'));
    }

    /**
     * API / AJAX: Cari Nota Transaksi Asli berdasarkan Invoice Code
     */
    public function searchTransaction(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $invoiceCode = trim($request->invoice_code);

        $transaction = Transaction::forStore($storeId)
            ->where('invoice_code', $invoiceCode)
            ->with(['details.product'])
            ->first();

        if (!$transaction) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nota transaksi tidak ditemukan di cabang ini!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $transaction
        ]);
    }

    /**
     * Halaman Form Penukaran Barang (OPTIMASI RINGAN)
     */
    public function create()
    {
        $categories = Category::with(['children.children'])->whereNull('parent_id')->get();
        
        // OPTIMASI: Pilih hanya kolom yang dibutuhkan untuk form retur agar RAM/Server tidak terbebankan
        $products = Product::where('is_active', true)
            ->select('id', 'category_id', 'name', 'code', 'type', 'cost_price', 'selling_price')
            ->get();

        return view('returns.create', compact('categories', 'products'));
    }

    /**
     * Eksekusi Simpan Penukaran / Retur Barang & Sinkronisasi Nota Transaksi
     */
    public function store(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('error', 'Akses ditolak! Anda harus membuka shift terlebih dahulu sebelum melakukan transaksi retur.');
        }

        $request->validate([
            'transaction_id'         => 'required|exists:transactions,id',
            'reason'                 => 'nullable|string|max:255',
            'returned_items'         => 'required|array|min:1',
            'returned_items.*.id'    => 'required|exists:products,id',
            'returned_items.*.qty'   => 'required|integer|min:1',
            'replacement_items'      => 'required|array|min:1',
            'replacement_items.*.id' => 'required|exists:products,id',
            'replacement_items.*.qty'=> 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($request->transaction_id);

            // OPTIMASI N+1: Ambil semua ID produk yang terlibat (retur + pengganti) sekaligus
            $allProductIds = collect($request->returned_items)->pluck('id')
                ->merge(collect($request->replacement_items)->pluck('id'))
                ->unique()
                ->toArray();

            // Load seluruh objek produk terkait dalam 1 kueri
            $productsList = Product::whereIn('id', $allProductIds)->get()->keyBy('id');

            // Load stok cabang untuk produk-produk terkait dalam 1 kueri
            $storeStocks = StoreProductStock::where('store_id', $storeId)
                ->whereIn('product_id', $allProductIds)
                ->pluck('stock', 'product_id');

            // 1. Hitung Nilai & Buat Data Barang yang Dikembalikan
            $returnedTotal = 0;
            $returnedDetailsData = [];
            foreach ($request->returned_items as $item) {
                if (!isset($item['id']) || !isset($item['qty'])) continue;

                $product = $productsList->get($item['id']);
                if (!$product) continue;

                $subtotal = $product->selling_price * $item['qty'];
                $returnedTotal += $subtotal;

                $returnedDetailsData[] = [
                    'product_id' => $product->id,
                    'type'       => 'returned',
                    'qty'        => (int) $item['qty'],
                    'price'      => (float) $product->selling_price,
                    'subtotal'   => (float) $subtotal,
                    'is_physical'=> strtolower(trim($product->type ?? 'physical')) === 'physical',
                ];
            }

            // 2. Hitung Nilai, Buat Data Barang Pengganti (Baru) & CEK STOK
            $replacementTotal = 0;
            $replacementCostTotal = 0;
            $replacementDetailsData = [];

            foreach ($request->replacement_items as $item) {
                if (!isset($item['id']) || !isset($item['qty'])) continue;

                $product = $productsList->get($item['id']);
                if (!$product) continue;

                $qty = (int) $item['qty'];

                // VALIDASI STOK DENGAN MEMPERHATIKAN ITEM RETUR
                if (strtolower(trim($product->type ?? 'physical')) === 'physical') {
                    $currentStock = $storeStocks->has($product->id) ? (int)$storeStocks->get($product->id) : (int)$product->stock;

                    // Tambahkan stok dari barang yang dikembalikan jika produknya sama
                    $returnedSameProductQty = 0;
                    foreach ($returnedDetailsData as $ret) {
                        if ($ret['product_id'] == $product->id) {
                            $returnedSameProductQty += $ret['qty'];
                        }
                    }
                    $effectiveStock = $currentStock + $returnedSameProductQty;

                    if ($effectiveStock < $qty) {
                        DB::rollBack();
                        return back()->with('error', 'Gagal memproses retur! Stok barang pengganti "' . $product->name . '" tidak mencukupi (Tersedia: ' . $effectiveStock . ' Pcs, Diminta: ' . $qty . ' Pcs).');
                    }
                }

                $subtotal = $product->selling_price * $qty;
                $costSubtotal = $product->cost_price * $qty;
                
                $replacementTotal += $subtotal;
                $replacementCostTotal += $costSubtotal;

                $replacementDetailsData[] = [
                    'product_id'    => $product->id,
                    'type'          => 'replacement',
                    'qty'           => $qty,
                    'price'         => (float) $product->selling_price,
                    'cost_price'    => (float) $product->cost_price,
                    'subtotal'      => (float) $subtotal,
                    'profit'        => (float) ($subtotal - $costSubtotal),
                    'is_physical'   => strtolower(trim($product->type ?? 'physical')) === 'physical',
                ];
            }

            // 3. Hitung Selisih & Cek Syarat Nominal (Pengganti WAJIB >= Retur)
            $priceDifference = $replacementTotal - $returnedTotal;

            if ($priceDifference < 0) {
                DB::rollBack();
                return back()->with('error', 'Gagal memproses retur! Nominal barang pengganti (Rp ' . number_format($replacementTotal, 0, ',', '.') . ') tidak boleh lebih murah dari barang yang dikembalikan (Rp ' . number_format($returnedTotal, 0, ',', '.') . ').');
            }

            // 4. Buat Record Retur Utama
            $productReturn = ProductReturn::create([
                'store_id'          => $storeId,
                'transaction_id'    => $transaction->id,
                'user_id'           => $transaction->user_id ?? Auth::id(),
                'shift_id'          => $activeShift->id,
                'return_code'       => 'RET-' . date('YmdHis') . '-' . rand(100, 999),
                'returned_total'    => $returnedTotal,
                'replacement_total' => $replacementTotal,
                'price_difference'  => $priceDifference,
                'payment_method'    => $request->payment_method ?? 'cash',
                'reason'            => $request->reason ?? 'Penukaran barang customer',
            ]);

            // 5. Simpan Detail Retur & Update Stok Fisik
            foreach (array_merge($returnedDetailsData, $replacementDetailsData) as $detail) {
                ReturnDetail::create([
                    'return_id'  => $productReturn->id,
                    'product_id' => $detail['product_id'],
                    'type'       => $detail['type'],
                    'qty'        => $detail['qty'],
                    'price'      => $detail['price'],
                    'subtotal'   => $detail['subtotal'],
                ]);

                if ($detail['is_physical']) {
                    $storeStock = StoreProductStock::firstOrCreate(
                        ['store_id' => $storeId, 'product_id' => $detail['product_id']],
                        ['stock' => 0]
                    );
                    $masterProduct = $productsList->get($detail['product_id']);

                    if ($detail['type'] === 'returned') {
                        $storeStock->increment('stock', $detail['qty']);
                        if ($masterProduct) $masterProduct->increment('stock', $detail['qty']);
                    } else {
                        $storeStock->decrement('stock', $detail['qty']);
                        if ($masterProduct) $masterProduct->decrement('stock', $detail['qty']);
                    }
                }
            }

            // 6. SINKRONISASI NOTA TRANSAKSI ASLI (`transactions`)
            foreach ($returnedDetailsData as $retItem) {
                $trxDetail = TransactionDetail::where('transaction_id', $transaction->id)
                    ->where('product_id', $retItem['product_id'])
                    ->first();

                if ($trxDetail) {
                    if ($trxDetail->qty <= $retItem['qty']) {
                        $trxDetail->delete();
                    } else {
                        $newQty = $trxDetail->qty - $retItem['qty'];
                        $trxDetail->update([
                            'qty'      => $newQty,
                            'subtotal' => $newQty * $trxDetail->selling_price,
                            'profit'   => $newQty * ($trxDetail->selling_price - $trxDetail->cost_price),
                        ]);
                    }
                }
            }

            foreach ($replacementDetailsData as $repItem) {
                $existingTrxDetail = TransactionDetail::where('transaction_id', $transaction->id)
                    ->where('product_id', $repItem['product_id'])
                    ->first();

                if ($existingTrxDetail) {
                    $newQty = $existingTrxDetail->qty + $repItem['qty'];
                    $existingTrxDetail->update([
                        'qty'      => $newQty,
                        'subtotal' => $newQty * $repItem['price'],
                        'profit'   => $newQty * ($repItem['price'] - $repItem['cost_price']),
                    ]);
                } else {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $repItem['product_id'],
                        'qty'            => $repItem['qty'],
                        'cost_price'     => $repItem['cost_price'],
                        'selling_price'  => $repItem['price'],
                        'subtotal'       => $repItem['subtotal'],
                        'profit'         => $repItem['profit'],
                    ]);
                }
            }

            // Update Total Nota Asli
            $updatedDetails = TransactionDetail::where('transaction_id', $transaction->id)->get();

            $newTotalCost   = $updatedDetails->sum(fn($d) => $d->cost_price * $d->qty);
            $newTotalPrice  = $updatedDetails->sum('subtotal');
            $newTotalProfit = $updatedDetails->sum('profit');

            $transaction->update([
                'total_cost'   => $newTotalCost,
                'total_price'  => $newTotalPrice,
                'total_profit' => $newTotalProfit,
                'pay_amount'   => $transaction->pay_amount + $priceDifference,
            ]);

            DB::commit();

            return redirect()->route('returns.index')->with('success', 'Transaksi retur berhasil diproses & nota transaksi telah diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses retur: ' . $e->getMessage());
        }
    }
}