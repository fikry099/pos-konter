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
     * Helper untuk mendapatkan biaya perak modal e-wallet/bank
     */
    private function getEwalletFeePerak(string $providerName): int
    {
        $provider = strtolower($providerName);

        if (str_contains($provider, 'dana')) return 101;
        if (str_contains($provider, 'gopay')) return 910;
        if (str_contains($provider, 'shopee')) return 75;
        if (str_contains($provider, 'ovo')) return 631;

        return 0; // Default untuk Bank / Transfer / Provider lainnya
    }

    /**
     * Helper menghitung Biaya Admin Standar
     */
    private function calculateAdminFee(float $nominal): float
    {
        $nominalInThousand = $nominal / 1000;

        if ($nominalInThousand < 100) {
            return 2000; // < 100rb = Admin 2.000
        } elseif ($nominalInThousand >= 100 && $nominalInThousand < 400) {
            return 3000; // 100rb - 399rb = Admin 3.000
        } else {
            return $nominal * 0.01; // >= 400rb = Admin 1%
        }
    }

    /**
     * Menampilkan Halaman Utama Kasir POS
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);

        // Load Kategori Utama
        $categories = Category::whereNull('parent_id')
            ->with(['allChildren'])
            ->get();

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
     * API ENDPOINT: Load Produk Berdasarkan Kategori & Provider/Sub-Kategori
     */
    public function getProductsByCategory(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $categoryKey = strtolower(trim($request->input('category', '')));
        $providerKey = strtolower(trim($request->input('provider', '')));

        $query = Product::query()
            ->select('products.*', DB::raw('COALESCE(store_product_stocks.stock, products.stock, 0) as current_stock'))
            ->leftJoin('store_product_stocks', function ($join) use ($storeId) {
                $join->on('products.id', '=', 'store_product_stocks.product_id')
                    ->where('store_product_stocks.store_id', '=', $storeId);
            })
            ->where('products.is_active', true)
            ->with(['category.parent']);

        $isAksesorisContext = str_contains($categoryKey, 'aksesoris') || str_contains($providerKey, 'aksesoris');
        $isHpContext        = str_contains($categoryKey, 'handphone') || str_contains($categoryKey, 'hp') || str_contains($providerKey, 'hp');

        if ($isAksesorisContext) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', 'like', '%aksesoris%')
                ->orWhere('name', 'like', '%aksesoris%')
                ->orWhereHas('parent', function ($p) {
                    $p->where('slug', 'like', '%aksesoris%')
                        ->orWhere('name', 'like', '%aksesoris%');
                });
            });

            if (!empty($providerKey) && !in_array($providerKey, ['aksesoris', 'aksesoris-hp', 'all'])) {
                $query->where(function ($q) use ($providerKey) {
                    // --- SUB-PROTEKSI ---
                    if ($providerKey === 'casing' || $providerKey === 'case') {
                        $q->where('products.name', 'like', '%case%')
                        ->orWhere('products.name', 'like', '%casing%')
                        ->orWhere('products.code', 'like', '%acc-case%');
                    } elseif ($providerKey === 'tempered-glass' || $providerKey === 'tg') {
                        $q->where('products.name', 'like', '%tg%')
                        ->orWhere('products.name', 'like', '%tempered%')
                        ->orWhere('products.code', 'like', '%acc-tg%');
                    } elseif ($providerKey === 'hydrogel') {
                        $q->where('products.name', 'like', '%hydrogel%')
                        ->orWhere('products.code', 'like', '%acc-hydrogel%');
                    } elseif ($providerKey === 'proteksi') {
                        $q->where('products.name', 'like', '%case%')
                        ->orWhere('products.name', 'like', '%casing%')
                        ->orWhere('products.name', 'like', '%tg%')
                        ->orWhere('products.name', 'like', '%hydrogel%')
                        ->orWhere('products.name', 'like', '%tempered%')
                        ->orWhere('products.code', 'like', '%acc-case%');

                    // --- SUB-POWER ---
                    } elseif ($providerKey === 'charger') {
                        $q->where('products.name', 'like', '%charger%')
                        ->orWhere('products.name', 'like', '%batok%')
                        ->orWhere('products.name', 'like', '%adaptor%')
                        ->orWhere('products.code', 'like', '%acc-charger%');
                    } elseif ($providerKey === 'kabel-data') {
                        $q->where('products.name', 'like', '%kabel%')
                        ->orWhere('products.name', 'like', '%cable%')
                        ->orWhere('products.code', 'like', '%acc-typec%')
                        ->orWhere('products.code', 'like', '%acc-iphone%');
                    } elseif ($providerKey === 'powerbank') {
                        $q->where('products.name', 'like', '%powerbank%')
                        ->orWhere('products.name', 'like', '%power bank%')
                        ->orWhere('products.name', 'like', '%pb%');
                    } elseif ($providerKey === 'power') {
                        $q->where('products.name', 'like', '%charger%')
                        ->orWhere('products.name', 'like', '%kabel%')
                        ->orWhere('products.name', 'like', '%power%')
                        ->orWhere('products.name', 'like', '%batok%')
                        ->orWhere('products.name', 'like', '%pb%');

                    // --- SUB-AUDIO ---
                    } elseif ($providerKey === 'tws') {
                        $q->where('products.name', 'like', '%tws%')
                        ->orWhere('products.name', 'like', '%earbuds%')
                        ->orWhere('products.name', 'like', '%airpods%');
                    } elseif ($providerKey === 'headset') {
                        $q->where('products.name', 'like', '%headset%')
                        ->orWhere('products.name', 'like', '%earphone%')
                        ->orWhere('products.code', 'like', '%acc-headset%');
                    } elseif ($providerKey === 'speaker') {
                        $q->where('products.name', 'like', '%speaker%')
                        ->orWhere('products.name', 'like', '%spiker%');
                    } elseif ($providerKey === 'audio') {
                        $q->where('products.name', 'like', '%headset%')
                        ->orWhere('products.name', 'like', '%tws%')
                        ->orWhere('products.name', 'like', '%speaker%')
                        ->orWhere('products.name', 'like', '%earphone%');

                    // --- SUB-PENYIMPANAN ---
                    } elseif ($providerKey === 'flashdisk') {
                        $q->where('products.name', 'like', '%flashdisk%')
                        ->orWhere('products.name', 'like', '%flash drive%')
                        ->orWhere('products.name', 'like', '%fd%');
                    } elseif ($providerKey === 'microsd') {
                        $q->where('products.name', 'like', '%microsd%')
                        ->orWhere('products.name', 'like', '%memory%')
                        ->orWhere('products.name', 'like', '%sd card%');
                    } elseif ($providerKey === 'penyimpanan') {
                        $q->where('products.name', 'like', '%flashdisk%')
                        ->orWhere('products.name', 'like', '%microsd%')
                        ->orWhere('products.name', 'like', '%memory%');

                    // --- SUB-MOUNT & STAND ---
                    } elseif ($providerKey === 'holder') {
                        $q->where('products.name', 'like', '%holder%')
                        ->orWhere('products.name', 'like', '%stand%');
                    } elseif ($providerKey === 'tripod') {
                        $q->where('products.name', 'like', '%tripod%')
                        ->orWhere('products.name', 'like', '%tongsis%')
                        ->orWhere('products.name', 'like', '%monopod%');
                    } elseif ($providerKey === 'mount-stand') {
                        $q->where('products.name', 'like', '%holder%')
                        ->orWhere('products.name', 'like', '%tripod%')
                        ->orWhere('products.name', 'like', '%stand%')
                        ->orWhere('products.name', 'like', '%tongsis%');
                    } else {
                        $cleanSearch = str_replace('-', ' ', $providerKey);
                        $q->where('products.name', 'like', "%{$providerKey}%")
                        ->orWhere('products.name', 'like', "%{$cleanSearch}%")
                        ->orWhere('products.code', 'like', "%{$providerKey}%");
                    }
                });
            }

        } elseif ($isHpContext) {
            // --- FILTER KATEGORI HANDPHONE ---
            $query->whereHas('category', function ($q) {
                $q->where('slug', 'like', '%handphone%')
                ->orWhere('slug', 'like', '%hp%')
                ->orWhere('name', 'like', '%handphone%')
                ->orWhere('name', 'like', '%hp%')
                ->orWhereHas('parent', function ($p) {
                    $p->where('slug', 'like', '%handphone%')
                        ->orWhere('slug', 'like', '%hp%')
                        ->orWhere('name', 'like', '%handphone%')
                        ->orWhere('name', 'like', '%hp%');
                });
            });

            if (!empty($providerKey) && !in_array($providerKey, ['handphone', 'hp', 'all'])) {
                $query->where(function ($q) use ($providerKey) {
                    if (in_array($providerKey, ['hp-new', 'new', 'baru'])) {
                        $q->where('products.name', 'like', '%new%')
                        ->orWhere('products.name', 'like', '%baru%')
                        ->orWhere('products.code', 'like', '%hp-new%')
                        ->orWhereHas('category', function($catQ) {
                            $catQ->where('slug', 'like', '%new%')
                                 ->orWhere('name', 'like', '%new%')
                                 ->orWhere('name', 'like', '%baru%');
                        });
                    } elseif (in_array($providerKey, ['hp-second', 'second', 'bekas', 'sec'])) {
                        $q->where('products.name', 'like', '%second%')
                        ->orWhere('products.name', 'like', '%bekas%')
                        ->orWhere('products.name', 'like', '%sec%')
                        ->orWhere('products.code', 'like', '%hp-sec%')
                        ->orWhere('products.code', 'like', '%hp-second%')
                        ->orWhereHas('category', function($catQ) {
                            $catQ->where('slug', 'like', '%second%')
                                 ->orWhere('name', 'like', '%second%')
                                 ->orWhere('name', 'like', '%bekas%');
                        });
                    } else {
                        $cleanSearch = str_replace('-', ' ', $providerKey);
                        $q->where('products.name', 'like', "%{$providerKey}%")
                        ->orWhere('products.name', 'like', "%{$cleanSearch}%")
                        ->orWhere('products.code', 'like', "%{$providerKey}%");
                    }
                });
            }

        } else {
            if (!empty($categoryKey) && $categoryKey !== 'all') {
                $query->whereHas('category', function ($q) use ($categoryKey) {
                    $q->where(function ($sub) use ($categoryKey) {
                        $sub->where('slug', 'like', "%{$categoryKey}%")
                            ->orWhere('name', 'like', "%{$categoryKey}%");
                    })
                    ->orWhereHas('parent', function ($p) use ($categoryKey) {
                        $p->where('slug', 'like', "%{$categoryKey}%")
                        ->orWhere('name', 'like', "%{$categoryKey}%");
                    });
                });
            }

            if (!empty($providerKey)) {
                $cleanProviderSearch = str_replace('-', ' ', $providerKey);

                $query->where(function ($q) use ($providerKey, $cleanProviderSearch) {
                    if (in_array($providerKey, ['tri', 'three', '3'])) {
                        $q->where('products.name', 'like', '%tri%')
                        ->orWhere('products.name', 'like', '%three%')
                        ->orWhere('products.code', 'like', '%v-3-%')
                        ->orWhere('products.code', 'like', '%tri%');
                    } elseif (in_array($providerKey, ['telkomsel', 'tsel'])) {
                        $q->where('products.name', 'like', '%telkomsel%')
                        ->orWhere('products.name', 'like', '%tsel%')
                        ->orWhere('products.name', 'like', '%by.u%')
                        ->orWhere('products.code', 'like', '%tsel%');
                    } elseif (in_array($providerKey, ['indosat', 'isat', 'im3'])) {
                        $q->where('products.name', 'like', '%indosat%')
                        ->orWhere('products.name', 'like', '%im3%')
                        ->orWhere('products.code', 'like', '%isat%');
                    } else {
                        $q->whereHas('category', function ($catQ) use ($providerKey) {
                            $catQ->where('slug', 'like', "%{$providerKey}%")
                                ->orWhere('name', 'like', "%{$providerKey}%");
                        })
                        ->orWhere('products.name', 'like', "%{$providerKey}%")
                        ->orWhere('products.name', 'like', "%{$cleanProviderSearch}%")
                        ->orWhere('products.code', 'like', "%{$providerKey}%");
                    }
                });
            }
        }

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
            'digital_provider' => 'nullable|string',
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
            $customPrice = (float) $request->input('custom_price', 0);
            
            // 1. Tangkap nama bank/wallet dari 'service_type' ATAU 'digital_provider'
            $serviceType = $request->input('service_type') ?? $request->input('digital_provider');
            if (!$serviceType || strtolower($serviceType) === 'transfer / top-up' || strtolower($serviceType) === 'transfer') {
                $serviceType = 'Nominal Bebas';
            }

            // 2. Hitung Modal (Cost Price)
            $feePerak  = $this->getEwalletFeePerak($serviceType);
            $costPrice = $customPrice + $feePerak;

            // 3. Hitung Admin & Selling Price
            $customInThousand = $customPrice / 1000;
            if (str_contains(strtolower($serviceType), 'maxim') && $customInThousand >= 10 && $customInThousand <= 100) {
                $defaultAdmin = 4000;
            } else {
                $defaultAdmin = $this->calculateAdminFee($customPrice);
            }

            $adminFee     = $request->filled('admin_fee') ? (float) $request->input('admin_fee') : $defaultAdmin;
            $sellingPrice = $customPrice + $adminFee;
            $profit       = $sellingPrice - $costPrice;

            $targetNum = $request->input('account_number') ?? $request->input('target_number');
            $accName   = $request->input('account_name');

            // 4. Buat Nama Tampilan secara Langsung dan Lengkap
            $serviceLower = strtolower($serviceType);
            $isBank = str_contains($serviceLower, 'bank') || str_contains($serviceLower, 'bca') || str_contains($serviceLower, 'bri') || str_contains($serviceLower, 'mandiri') || str_contains($serviceLower, 'bni') || str_contains($serviceLower, 'bsi');
            $prefix = $isBank ? 'Transfer' : 'Top-Up';

            // Format: "Transfer BANK BCA 1.111.847.843 (z)"
            $displayName = $prefix . ' ' . strtoupper($serviceType) . ' ' . number_format($sellingPrice, 0, ',', '.');
            if ($accName) {
                $displayName .= ' (' . $accName . ')';
            }

            $cartKey = 'custom_' . time() . '_' . rand(100, 999);

            $cart[$cartKey] = [
                'product_id'        => $request->product_id ?? 1,
                'name'              => $displayName, // Disimpan utuh ke session
                'type'              => 'digital',
                'target_phone'      => $targetNum,
                'cost_price'        => $costPrice,
                'selling_price'     => $sellingPrice,
                'qty'               => 1,
                'subtotal'          => $sellingPrice,
                'profit'            => $profit,
                'digital_provider'  => 'Propana', // PPOB Server Provider
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
                    return back()->with('error', 'Gagal! Stok "' . $product->name . '" tidak mencukupi.');
                }
            }

            $defaultProvider = $request->input('digital_provider', null);

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['qty'] += $qty;
                $cart[$cartKey]['subtotal'] = $cart[$cartKey]['qty'] * $cart[$cartKey]['selling_price'];
                $cart[$cartKey]['profit']   = $cart[$cartKey]['qty'] * ($cart[$cartKey]['selling_price'] - $cart[$cartKey]['cost_price']);
                if ($isDigital && $request->filled('digital_provider')) {
                    $cart[$cartKey]['digital_provider'] = $request->input('digital_provider');
                }
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
                    'digital_provider'  => $defaultProvider,
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
            $staffId = $request->served_by_user_id ? (int) $request->served_by_user_id : null;
            $cart[$key]['served_by_user_id'] = $staffId;
            session()->put('pos_cart', $cart);

            if ($request->ajax() || $request->wantsJson()) {
                $staff = User::find($staffId);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Penanggung jawab berhasil diperbarui',
                    'staff_id' => $staffId,
                    'staff_name' => $staff ? $staff->name : 'Pilih Karyawan Penjual'
                ]);
            }
        }

        return back()->with('success', 'Penanggung jawab item berhasil diperbarui.');
    }

    public function updateCart(Request $request, $key)
    {
        $storeId = $this->getActiveStoreId();
        $cart = session()->get('pos_cart', []);

        if (isset($cart[$key])) {
            if ($request->has('digital_provider')) {
                $cart[$key]['digital_provider'] = $request->input('digital_provider');
            }

            if ($request->has('qty')) {
                $qty = (int) $request->qty;
                if ($qty > 0) {
                    $item = $cart[$key];
                    $productType = strtolower(trim($item['type'] ?? 'physical'));

                    if ($productType !== 'digital') {
                        $availableStock = $this->getProductStock($storeId, $item['product_id']);
                        if ($qty > $availableStock) {
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json(['status' => 'error', 'message' => 'Jumlah melebihi stok!'], 422);
                            }
                            return back()->with('error', 'Jumlah melebihi stok yang tersedia!');
                        }
                    }

                    $cart[$key]['qty']      = $qty;
                    $cart[$key]['subtotal'] = $qty * $cart[$key]['selling_price'];
                    $cart[$key]['profit']   = $qty * ($cart[$key]['selling_price'] - $cart[$key]['cost_price']);
                } else {
                    unset($cart[$key]);
                }
            }

            session()->put('pos_cart', $cart);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Server provider berhasil diperbarui',
                'provider' => $cart[$key]['digital_provider'] ?? null
            ]);
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

    /**
     * Menyimpan Transaksi Pembelian ke Database
     */
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
                    return back()->with('error', 'Transaksi dibatalkan! Stok untuk "' . $item['name'] . '" telah habis.');
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
                    if (!empty($item['digital_provider'])) {
                        $provider = $item['digital_provider'];
                    } elseif ($request->filled('digital_provider')) {
                        $provider = $request->digital_provider;
                    }
                }

                TransactionDetail::create([
                    'transaction_id'    => $transaction->id,
                    'product_id'        => $item['product_id'],
                    'custom_name'       => $item['name'] ?? null,
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