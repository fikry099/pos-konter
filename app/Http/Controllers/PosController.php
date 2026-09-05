<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StoreProductStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    private function getActiveStoreId()
    {
        return Auth::user()->store_id ?? session('selected_store_id') ?? 1;
    }

    private function getProductStock($storeId, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        $storeStock = StoreProductStock::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        if ($storeStock !== null) {
            return (int) $storeStock->stock;
        }

        return (int) ($product->stock ?? 0);
    }

    /**
     * Menampilkan Halaman Utama Kasir POS (Fast Initial Load Tanpa Produk)
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        // Load Kategori Utama
        $categories = Category::whereNull('parent_id')
            ->with(['allChildren'])
            ->get();

        // Kosongkan produk awal agar halaman terbuka instan (0.1 detik)
        $products = collect();

        // Ambil Karyawan Shift
        $shiftStaffs = collect();
        if ($activeShift) {
            if ($activeShift->user_ids && is_array($activeShift->user_ids)) {
                $shiftStaffs = User::whereIn('id', $activeShift->user_ids)->get();
            } elseif ($activeShift->user) {
                $shiftStaffs = collect([$activeShift->user]);
            }
        }

        // Sinkronisasi Keranjang
        $cart = session()->get('pos_cart', []);
        if ($shiftStaffs->count() === 1) {
            $defaultUserId = $shiftStaffs->first()->id;
            $updated = false;
            foreach ($cart as $k => $v) {
                $pType = strtolower(trim($v['type'] ?? 'physical'));
                if ($pType !== 'digital' && empty($v['served_by_user_id'])) {
                    $cart[$k]['served_by_user_id'] = $defaultUserId;
                    $updated = true;
                }
            }
            if ($updated) {
                session()->put('pos_cart', $cart);
            }
        }

        return view('pos.index', compact('activeShift', 'categories', 'products', 'cart', 'shiftStaffs'));
    }

    /**
     * METHOD BARU: API Endpoint Load Produk On-Demand Tanpa Pagination per Kategori/Provider
     */
    public function getProductsByCategory(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $categoryKey = strtolower(trim($request->input('category', '')));
        $providerKey = strtolower(trim($request->input('provider', '')));

        // Kueri dasar dengan leftJoin stok cabang (Bebas N+1 Query)
        $query = Product::query()
            ->select('products.*', DB::raw('COALESCE(store_product_stocks.stock, products.stock, 0) as current_stock'))
            ->leftJoin('store_product_stocks', function ($join) use ($storeId) {
                $join->on('products.id', '=', 'store_product_stocks.product_id')
                     ->where('store_product_stocks.store_id', '=', $storeId);
            })
            ->where('products.is_active', true)
            ->with(['category.parent']);

        // 1. Filter Provider / Operator Spesifik
        if (!empty($providerKey)) {
            $query->where(function ($q) use ($providerKey) {
                $q->where('products.name', 'like', "%{$providerKey}%")
                  ->orWhere('products.code', 'like', "%{$providerKey}%");
            });
        } 
        // 2. Filter berdasarkan Kategori
        elseif (!empty($categoryKey) && $categoryKey !== 'all') {
            $query->whereHas('category', function ($q) use ($categoryKey) {
                $q->where('slug', 'like', "%{$categoryKey}%")
                  ->orWhere('name', 'like', "%{$categoryKey}%");
            });
        }

        // Ambil SELURUH produk yang sesuai tanpa dipotong pagination
        $products = $query->get();

        return response()->json($products);
    }

    /**
     * Menambahkan Item ke Keranjang
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id'       => 'nullable',
            'is_custom_amount' => 'nullable|boolean',
            'custom_price'     => 'required_if:is_custom_amount,1|nullable|numeric|min:1000',
            'admin_fee'        => 'nullable|numeric|min:0',
            'target_number'    => 'nullable|string',
            'account_number'   => 'nullable|string',
            'account_name'     => 'nullable|string',
            'quantity'         => 'nullable|integer|min:1',
            'qty'              => 'nullable|integer|min:1',
        ]);

        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);
        $cart = session()->get('pos_cart', []);
        $isCustom = $request->input('is_custom_amount') == '1';

        $defaultStaffId = null;
        if ($activeShift) {
            if (is_array($activeShift->user_ids) && count($activeShift->user_ids) === 1) {
                $defaultStaffId = $activeShift->user_ids[0];
            } elseif ($activeShift->user_id) {
                $defaultStaffId = $activeShift->user_id;
            }
        }

        if ($isCustom) {
            $customPrice  = (float) $request->input('custom_price', 0);
            $adminFee     = (float) $request->input('admin_fee', 2500);
            $sellingPrice = $customPrice + $adminFee;
            $costPrice    = $customPrice;

            $targetNum = $request->input('account_number') ?? $request->input('target_number');
            $accName   = $request->input('account_name');

            $displayName = 'Transfer / Top-Up Nominal Bebas';
            if ($accName) {
                $displayName .= ' (' . $accName . ')';
            }

            $cartKey = 'custom_' . time() . '_' . rand(100, 999);

            $cart[$cartKey] = [
                'product_id'        => $request->product_id ?? 1,
                'name'              => $displayName,
                'type'              => 'digital',
                'target_phone'      => $targetNum,
                'cost_price'        => $costPrice,
                'selling_price'     => $sellingPrice,
                'qty'               => 1,
                'subtotal'          => $sellingPrice,
                'profit'            => $adminFee,
                'served_by_user_id' => null,
            ];

            session()->put('pos_cart', $cart);
            return back()->with('success', 'Transaksi nominal bebas ditambahkan ke keranjang.');

        } else {
            $productId = $request->product_id;
            if (!$productId) {
                return back()->with('error', 'Produk tidak ditemukan!');
            }

            $product = Product::findOrFail($productId);
            $qty     = (int) ($request->quantity ?? $request->qty ?? 1);

            $targetNum = $request->input('target_number') ?? $request->input('account_number') ?? $request->input('target_phone');
            $cartKey   = $product->id . ($targetNum ? '_' . $targetNum : '');

            $productType = strtolower(trim($product->type ?? 'physical'));
            $isDigital   = ($productType === 'digital');

            if (!$isDigital) {
                $availableStock = $this->getProductStock($storeId, $product->id);
                $existingQtyInCart = isset($cart[$cartKey]) ? (int) $cart[$cartKey]['qty'] : 0;
                $totalRequested = $existingQtyInCart + $qty;

                if ($availableStock <= 0) {
                    return back()->with('error', 'Gagal! Stok produk "' . $product->name . '" telah HABIS (0 Pcs).');
                }

                if ($totalRequested > $availableStock) {
                    return back()->with('error', 'Gagal! Stok "' . $product->name . '" tidak mencukupi (Tersedia: ' . $availableStock . ' Pcs, Di keranjang: ' . $existingQtyInCart . ' Pcs).');
                }
            }

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['qty'] += $qty;
                $cart[$cartKey]['subtotal'] = $cart[$cartKey]['qty'] * $cart[$cartKey]['selling_price'];
                $cart[$cartKey]['profit']   = $cart[$cartKey]['qty'] * ($cart[$cartKey]['selling_price'] - $cart[$cartKey]['cost_price']);
            } else {
                $cart[$cartKey] = [
                    'product_id'        => $product->id,
                    'name'              => $product->name,
                    'type'              => $product->type,
                    'target_phone'      => $targetNum,
                    'cost_price'        => (float) $product->cost_price,
                    'selling_price'     => (float) $product->selling_price,
                    'qty'               => $qty,
                    'subtotal'          => (float) ($product->selling_price * $qty),
                    'profit'            => (float) (($product->selling_price - $product->cost_price) * $qty),
                    'served_by_user_id' => $isDigital ? null : $defaultStaffId,
                ];
            }

            session()->put('pos_cart', $cart);
            return back()->with('success', $product->name . ' ditambahkan ke keranjang.');
        }
    }

    public function assignStaff(Request $request, $key)
    {
        $cart = session()->get('pos_cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['served_by_user_id'] = $request->served_by_user_id ? (int) $request->served_by_user_id : null;
            session()->put('pos_cart', $cart);
        }

        return back()->with('success', 'Penanggung jawab item berhasil diperbarui.');
    }

    public function updateCart(Request $request, $key)
    {
        $storeId = $this->getActiveStoreId();
        $cart = session()->get('pos_cart', []);

        if (isset($cart[$key])) {
            $qty = (int) $request->qty;
            if ($qty > 0) {
                $item = $cart[$key];
                $productType = strtolower(trim($item['type'] ?? 'physical'));

                if ($productType !== 'digital') {
                    $availableStock = $this->getProductStock($storeId, $item['product_id']);
                    if ($qty > $availableStock) {
                        return back()->with('error', 'Jumlah melebihi stok yang tersedia! Stok "' . $item['name'] . '" tersisa: ' . $availableStock . ' Pcs.');
                    }
                }

                $cart[$key]['qty']      = $qty;
                $cart[$key]['subtotal'] = $qty * $cart[$key]['selling_price'];
                $cart[$key]['profit']   = $qty * ($cart[$key]['selling_price'] - $cart[$key]['cost_price']);
                session()->put('pos_cart', $cart);
            } else {
                unset($cart[$key]);
                session()->put('pos_cart', $cart);
            }
        }

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeFromCart($key)
    {
        $cart = session()->get('pos_cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('pos_cart', $cart);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function clearCart()
    {
        session()->forget('pos_cart');
        return back()->with('success', 'Keranjang dikosongkan.');
    }

    public function store(Request $request)
    {
        $cart = session()->get('pos_cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang belanja masih kosong!');
        }

        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('error', 'Shift tidak aktif! Buka shift terlebih dahulu.');
        }

        foreach ($cart as $item) {
            $productType = strtolower(trim($item['type'] ?? 'physical'));
            if ($productType !== 'digital') {
                if (empty($item['served_by_user_id'])) {
                    return back()->with('error', 'Gagal Checkout! Harap pilih Karyawan Penanggung Jawab untuk item aksesoris "' . $item['name'] . '" di keranjang.');
                }

                $availableStock = $this->getProductStock($storeId, $item['product_id']);
                if ($availableStock < $item['qty']) {
                    return back()->with('error', 'Transaksi dibatalkan! Stok untuk "' . $item['name'] . '" telah habis/tidak mencukupi (Tersedia: ' . $availableStock . ' Pcs, Diminta: ' . $item['qty'] . ' Pcs).');
                }
            }
        }

        $totalPrice  = array_sum(array_column($cart, 'subtotal'));
        $totalCost   = array_sum(array_map(fn($item) => $item['cost_price'] * $item['qty'], $cart));
        $totalProfit = array_sum(array_column($cart, 'profit'));

        $request->validate([
            'payment_method'   => 'required|in:cash,qris',
            'pay_amount'       => 'required|numeric|min:' . $totalPrice,
            'payment_proof'    => 'required_if:payment_method,qris',
            'digital_provider' => 'nullable|string',
        ], [
            'pay_amount.min'   => 'Uang pembayaran kurang! Total tagihan adalah Rp ' . number_format($totalPrice, 0, ',', '.'),
        ]);

        $payAmount    = (float) $request->pay_amount;
        $changeAmount = $payAmount - $totalPrice;

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'store_id'       => $storeId,
                'invoice_code'   => 'TRX-' . date('YmdHis') . '-' . rand(100, 999),
                'user_id'        => Auth::id() ?? $activeShift->user_id,
                'shift_id'       => $activeShift->id,
                'total_cost'     => $totalCost,
                'total_price'    => $totalPrice,
                'total_profit'   => $totalProfit,
                'pay_amount'     => $payAmount,
                'change_amount'  => $changeAmount,
                'payment_method' => $request->payment_method,
                'payment_proof'  => $request->payment_method === 'qris' ? $request->payment_proof : null,
            ]);

            foreach ($cart as $item) {
                $provider = null;
                $productType = strtolower(trim($item['type'] ?? 'physical'));

                if ($productType === 'digital') {
                    if ($request->filled('digital_provider')) {
                        $provider = $request->digital_provider;
                    } else {
                        $itemName = strtolower($item['name'] ?? '');
                        
                        if (str_contains($itemName, 'transfer') || str_contains($itemName, 'top-up') || 
                            str_contains($itemName, 'dana') || str_contains($itemName, 'gopay') || 
                            str_contains($itemName, 'ovo') || str_contains($itemName, 'shopee') || 
                            str_contains($itemName, 'linkaja') || str_contains($itemName, 'bank')) {
                            $provider = 'Propana';
                        } else {
                            $provider = 'Digipos';
                        }
                    }
                }

                TransactionDetail::create([
                    'transaction_id'    => $transaction->id,
                    'product_id'        => $item['product_id'],
                    'served_by_user_id' => $productType !== 'digital' ? $item['served_by_user_id'] : null,
                    'target_phone'      => $item['target_phone'],
                    'digital_provider'  => $provider,
                    'qty'               => $item['qty'],
                    'cost_price'        => $item['cost_price'],
                    'selling_price'     => $item['selling_price'],
                    'subtotal'          => $item['subtotal'],
                    'profit'            => $item['profit'],
                ]);

                if ($productType !== 'digital') {
                    $stock = StoreProductStock::where('store_id', $storeId)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if ($stock) {
                        $stock->decrement('stock', $item['qty']);
                    }

                    $masterProduct = Product::find($item['product_id']);
                    if ($masterProduct) {
                        $masterProduct->decrement('stock', $item['qty']);
                    }
                }
            }

            DB::commit();

            session()->forget('pos_cart');

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}