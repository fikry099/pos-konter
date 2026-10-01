<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StoreProductStock;
use App\Models\PpobServer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Helper privat menentukan store_id cabang aktif (prioritaskan Tab Session)
     */
    private function getActiveStoreId()
    {
        return session('selected_store_id') ?? Auth::user()->store_id ?? 1;
    }

    private function getProductStock($storeId, $productId)
    {
        if (!$productId) {
            return 0;
        }
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
     * Helper kalkulasi Modal Riil (HPP) khusus Transfer Bank berdasarkan Provider / Aplikasi yang digunakan
     */
    private function calculateBankCostPrice(float $nominal, string $providerName): float
    {
        $provider = strtolower(trim($providerName));

        // 1. Jika via Seabank -> Admin Modal 0 Rp
        if (str_contains($provider, 'seabank')) {
            return $nominal;
        }

        // 2. Jika via Mitra Shopee -> Admin Modal 100 Rp
        if (str_contains($provider, 'shopee') || str_contains($provider, 'mitra shopee')) {
            return $nominal + 100;
        }

        // 3. Default / Propana / Server Lainnya
        return $nominal;
    }

    /**
     * Helper biaya perak modal e-wallet/bank/PLN
     */
    private function getEwalletFeePerak(string $providerName): int
    {
        $provider = strtolower($providerName);
        if (str_contains($provider, 'dana')) return 101;
        if (str_contains($provider, 'gopay')) return 910;
        if (str_contains($provider, 'shopee')) return 75;
        if (str_contains($provider, 'ovo')) return 631;
        if (str_contains($provider, 'pln')) return 511; // Fee perak PLN Token nominal 5k-15k
        return 0;
    }

    /**
     * Helper kalkulasi modal khusus Token PLN berdasarkan catatan nominal
     */
    private function calculatePlnCostPrice(float $nominal): float
    {
        $nominalInt = (int) $nominal;
        return match ($nominalInt) {
            20000   => 21214,
            25000   => 26511,
            50000   => 51261,
            100000  => 101321,
            200000  => 201321,
            500000  => 501321,
            1000000 => 1001321,
            default => $nominal + 511,
        };
    }

    /**
     * Helper menghitung Biaya Admin Standar
     */
    private function calculateAdminFee(float $nominal): float
    {
        $nominalInThousand = $nominal / 1000;
        if ($nominalInThousand < 100) {
            return 2000;
        } elseif ($nominalInThousand >= 100 && $nominalInThousand < 400) {
            return 3000;
        } else {
            return $nominal * 0.01;
        }
    }

    /**
     * Menampilkan Halaman Utama Kasir POS
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $activeShift = Shift::getActiveShift($storeId);
        $categories = Category::whereNull('parent_id')
            ->with(['allChildren'])
            ->get();
        $products = collect();
        $shiftStaffs = collect();
        if ($activeShift) {
            if ($activeShift->user_ids && is_array($activeShift->user_ids)) {
                $shiftStaffs = User::whereIn('id', $activeShift->user_ids)->get();
            } elseif ($activeShift->user) {
                $shiftStaffs = collect([$activeShift->user]);
            }
        }
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
     * API ENDPOINT: Load Produk (PEMFILTERAN MURNI HIRARKI KATEGORI PRESISI)
     */
    public function getProductsByCategory(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        $categoryKey = strtolower(trim($request->input('category', '')));
        $providerKey = strtolower(trim($request->input('provider', '')));

        // Override selling_price & cost_price dengan data dari tabel store_product_stocks
        $query = Product::query()
            ->select(
                'products.*',
                DB::raw('COALESCE(store_product_stocks.stock, 0) as current_stock'),
                DB::raw('COALESCE(store_product_stocks.cost_price, products.cost_price, 0) as cost_price'),
                DB::raw('COALESCE(store_product_stocks.selling_price, products.selling_price, 0) as selling_price')
            )
            ->where(function ($q) use ($storeId) {
                $q->where('products.type', '=', 'digital')
                  ->orWhereHas('storeStocks', function ($qs) use ($storeId) {
                      $qs->where('store_id', '=', $storeId);
                  });
            })
            ->leftJoin('store_product_stocks', function ($join) use ($storeId) {
                $join->on('products.id', '=', 'store_product_stocks.product_id')
                    ->where('store_product_stocks.store_id', '=', $storeId);
            })
            ->where('products.is_active', true)
            ->with(['category.parent']);

        $isAksesorisContext = str_contains($categoryKey, 'aksesoris') || str_contains($categoryKey, 'acc') || str_contains($providerKey, 'aksesoris');
        $isHpContext        = str_contains($categoryKey, 'handphone') || str_contains($categoryKey, 'hp') || str_contains($providerKey, 'hp');

        // ==========================================
        // 1. FILTER KHUSUS AKSESORIS HP (PRESISI MURNI SLUG)
        // ==========================================
        if ($isAksesorisContext) {
            // Pastikan produk milik Kategori Induk "Aksesoris" atau Sub-kategorinya
            $query->whereHas('category', function ($q) {
                $q->where('slug', 'like', '%aksesoris%')
                ->orWhere('name', 'like', '%aksesoris%')
                ->orWhere('slug', 'like', '%acc%')
                ->orWhereHas('parent', function ($p) {
                    $p->where('slug', 'like', '%aksesoris%')
                        ->orWhere('name', 'like', '%aksesoris%')
                        ->orWhere('slug', 'like', '%acc%');
                });
            });

            // Jika memilih Sub-Katalog Aksesoris tertentu
            if (!empty($providerKey) && !in_array($providerKey, ['aksesoris', 'aksesoris-hp', 'all'])) {
                $query->whereHas('category', function ($catQ) use ($providerKey) {
                    
                    // A. Cable Data & AUX
                    if (in_array($providerKey, ['cable-data-aux', 'kabel-data', 'cable', 'kabel', 'aux'])) {
                        $catQ->whereIn('slug', ['cable-data-aux', 'kabel-data', 'cable', 'kabel', 'aux'])
                             ->orWhere('name', 'like', '%cable%')
                             ->orWhere('name', 'like', '%kabel%');
                    } 
                    // B. Adaptor / Kepala Charger (EKSKLUSIF TANPA SET / CAR CHARGER)
                    elseif (in_array($providerKey, ['adaptor-kepala', 'charger', 'adaptor', 'kepala-charger'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'adaptor-kepala')
                                ->orWhere('slug', 'kepala-charger')
                                ->orWhere('slug', 'adaptor')
                                ->orWhere(function($exact) {
                                    $exact->where('name', 'like', '%adaptor%')
                                          ->where('name', 'not like', '%set%')
                                          ->where('name', 'not like', '%car%');
                                });
                        });
                    } 
                    // C. Adaptor 1Set & Car Charger (EKSKLUSIF UNTUK CHARGER SET & CAR)
                    elseif (in_array($providerKey, ['adaptor-set-car', 'charger-set', 'car-charger'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'adaptor-set-car')
                                ->orWhere('slug', 'charger-set')
                                ->orWhere('slug', 'car-charger')
                                ->orWhere('name', 'like', '%set%')
                                ->orWhere('name', 'like', '%car%');
                        });
                    } 
                    // D. Headset / Earphone / TWS
                    elseif (in_array($providerKey, ['headset-earphone', 'headset', 'tws', 'earphone', 'audio'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'like', '%headset%')
                                ->orWhere('slug', 'like', '%earphone%')
                                ->orWhere('slug', 'like', '%tws%')
                                ->orWhere('name', 'like', '%headset%')
                                ->orWhere('name', 'like', '%earphone%')
                                ->orWhere('name', 'like', '%tws%');
                        });
                    } 
                    // E. MMC & Flashdisk
                    elseif (in_array($providerKey, ['mmc-flashdisk', 'flashdisk', 'microsd', 'memory', 'penyimpanan'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'like', '%flashdisk%')
                                ->orWhere('slug', 'like', '%mmc%')
                                ->orWhere('name', 'like', '%flashdisk%')
                                ->orWhere('name', 'like', '%mmc%');
                        });
                    } 
                    // F. Powerbank
                    elseif (in_array($providerKey, ['powerbank', 'pb', 'power-bank'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'like', '%powerbank%')
                                ->orWhere('name', 'like', '%powerbank%');
                        });
                    } 
                    // G. Softcase / Hardcase / Casing
                    elseif (in_array($providerKey, ['softcase', 'casing', 'case', 'hardcase', 'proteksi'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'like', '%case%')
                                ->orWhere('name', 'like', '%case%');
                        });
                    } 
                    // H. Antigores / Tempered Glass / Hydrogel
                    elseif (in_array($providerKey, ['antigores', 'tempered-glass', 'tg', 'hydrogel'])) {
                        $catQ->where(function($sub) {
                            $sub->where('slug', 'like', '%antigores%')
                                ->orWhere('name', 'like', '%antigores%');
                        });
                    } 
                    // I. Filter Merek / Slug Spesifik Lainnya
                    else {
                        $cleanSearch = str_replace('-', ' ', $providerKey);
                        $catQ->where('slug', '=', $providerKey)
                             ->orWhere('name', 'like', "%{$cleanSearch}%");
                    }
                });
            }
        } 
        // ==========================================
        // 2. FILTER HANDPHONE
        // ==========================================
        elseif ($isHpContext) {
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
                $query->whereHas('category', function($catQ) use ($providerKey) {
                    if (in_array($providerKey, ['hp-new', 'new', 'baru'])) {
                        $catQ->where('slug', 'like', '%new%')
                             ->orWhere('name', 'like', '%new%')
                             ->orWhere('name', 'like', '%baru%');
                    } elseif (in_array($providerKey, ['hp-second', 'second', 'bekas', 'sec'])) {
                        $catQ->where('slug', 'like', '%second%')
                             ->orWhere('name', 'like', '%second%')
                             ->orWhere('name', 'like', '%bekas%');
                    } else {
                        $cleanSearch = str_replace('-', ' ', $providerKey);
                        $catQ->where('slug', 'like', "%{$providerKey}%")
                             ->orWhere('name', 'like', "%{$providerKey}%")
                             ->orWhere('name', 'like', "%{$cleanSearch}%");
                    }
                });
            }
        } 
        // ==========================================
        // 3. FILTER KATEGORI UMUM / PPOB / PULSA / LAINNYA
        // ==========================================
        else {
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
                    } elseif (in_array($providerKey, ['seabank', 'jago', 'cimb', 'permata', 'danamon', 'btn', 'bpd'])) {
                        $q->where('products.name', 'like', "%{$providerKey}%")
                        ->orWhere('products.name', 'like', "%{$cleanProviderSearch}%")
                        ->orWhere('products.code', 'like', "%{$providerKey}%")
                        ->orWhereHas('category', function ($catQ) use ($providerKey) {
                            $catQ->where('slug', 'like', "%{$providerKey}%")
                                ->orWhere('name', 'like', "%{$providerKey}%");
                        });
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
     * Menyimpan Item ke Keranjang
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id'        => 'nullable',
            'is_custom_amount' => 'nullable|boolean',
            'is_quota_inject'  => 'nullable|boolean',
            'custom_price'     => 'required_if:is_custom_amount,1|nullable|numeric|min:1000',
            'package_name'     => 'required_if:is_quota_inject,1|nullable|string',
            'cost_price'        => 'required_if:is_quota_inject,1|nullable|numeric|min:0',
            'selling_price'    => 'required_if:is_quota_inject,1|nullable|numeric|min:0',
            'admin_fee'         => 'nullable|numeric|min:0',
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
        $isQuotaInject = $request->input('is_quota_inject') == '1';

        $defaultStaffId = null;
        if ($activeShift) {
            if (is_array($activeShift->user_ids) && count($activeShift->user_ids) === 1) {
                $defaultStaffId = $activeShift->user_ids[0];
            } elseif ($activeShift->user_id) {
                $defaultStaffId = $activeShift->user_id;
            }
        }

        // --- PENANGANAN KHUSUS KUOTA TEMBAK ---
        if ($isQuotaInject) {
            $packageName  = trim($request->input('package_name', 'Kuota Tembak'));
            $costPrice    = (float) $request->input('cost_price', 0);
            $sellingPrice = (float) $request->input('selling_price', 0);
            $profit       = $sellingPrice - $costPrice;

            $targetNum = $request->input('target_number');
            if (empty(trim((string)$targetNum))) {
                $targetNum = '-';
            }

            $displayName = 'Kuota Tembak - ' . $packageName;

            $cartKey = 'quota_' . time() . '_' . rand(100, 999);
            $finalProductId = ($request->filled('product_id') && $request->product_id != 1) ? $request->product_id : null;

            $cart[$cartKey] = [
                'product_id'        => $finalProductId,
                'name'              => $displayName,
                'type'              => 'digital',
                'target_phone'      => $targetNum,
                'cost_price'        => $costPrice,
                'selling_price'     => $sellingPrice,
                'qty'               => 1,
                'subtotal'          => $sellingPrice,
                'profit'            => $profit,
                'digital_provider'  => $request->input('digital_provider') ?? \App\Models\PpobServer::where('name', 'Propana')->value('name') ?? 'Propana',
                'served_by_user_id' => null,
            ];

            session()->put('pos_cart', $cart);
            return back()->with('success', 'Kuota tembak ditambahkan ke keranjang.');
        }

        // --- PENANGANAN NOMINAL BEBAS (TOP-UP / TRANSFER BEBAS / PLN) ---
        if ($isCustom) {
            $customPrice = (float) $request->input('custom_price', 0);
            
            $serviceType = $request->input('service_type') ?? $request->input('digital_provider');
            if (!$serviceType || strtolower($serviceType) === 'transfer / top-up' || strtolower($serviceType) === 'transfer') {
                $serviceType = 'Nominal Bebas';
            }

            $serviceLower = strtolower($serviceType);

            $bankKeywords = ['bank', 'bca', 'bri', 'mandiri', 'bni', 'bsi', 'seabank', 'jago', 'cimb', 'permata', 'danamon', 'btn', 'bpd', 'transfer'];
            $isBank = false;
            foreach ($bankKeywords as $kw) {
                if (str_contains($serviceLower, $kw)) {
                    $isBank = true;
                    break;
                }
            }

            $defaultProvider = $request->input('digital_provider') ?? \App\Models\PpobServer::where('name', 'Propana')->value('name') ?? 'Propana';

            // LOGIKA HITUNG MODAL RIIL (HPP)
            if ($isBank) {
                $costPrice = $this->calculateBankCostPrice($customPrice, $defaultProvider);
            } elseif (str_contains($serviceLower, 'pln')) {
                $costPrice = $this->calculatePlnCostPrice($customPrice);
            } else {
                $feePerak   = $this->getEwalletFeePerak($serviceType);
                $costPrice = $customPrice + $feePerak;
            }

            $customInThousand = $customPrice / 1000;
            if (str_contains($serviceLower, 'maxim') && $customInThousand >= 10 && $customInThousand <= 100) {
                $defaultAdmin = 4000;
            } else {
                $defaultAdmin = $this->calculateAdminFee($customPrice);
            }

            $adminFee     = $request->filled('admin_fee') ? (float) $request->input('admin_fee') : $defaultAdmin;
            $sellingPrice = $customPrice + $adminFee;
            $profit       = $sellingPrice - $costPrice;
            
            $targetNum = $request->input('account_number') ?? $request->input('target_number');
            if (empty(trim((string)$targetNum))) {
                $targetNum = '-';
            }
            $accName = $request->input('account_name');

            $prefix = $isBank ? 'Transfer' : (str_contains($serviceLower, 'pln') ? 'Token' : 'Top-Up');
            $displayName = $prefix . ' ' . strtoupper($serviceType) . ' ' . number_format($sellingPrice, 0, ',', '.');
            if ($accName) {
                $displayName .= ' (' . $accName . ')';
            }

            $cartKey = 'custom_' . time() . '_' . rand(100, 999);
            $finalProductId = ($request->filled('product_id') && $request->product_id != 1) ? $request->product_id : null;

            $cart[$cartKey] = [
                'product_id'        => $finalProductId,
                'name'              => $displayName,
                'type'              => 'digital',
                'target_phone'      => $targetNum,
                'custom_amount'     => $customPrice,
                'cost_price'        => $costPrice,
                'selling_price'     => $sellingPrice,
                'qty'               => 1,
                'subtotal'          => $sellingPrice,
                'profit'            => $profit,
                'digital_provider'  => $defaultProvider,
                'served_by_user_id' => null,
            ];

            session()->put('pos_cart', $cart);
            return back()->with('success', 'Transaksi nominal bebas ditambahkan ke keranjang.');
        } else {
            // --- UNTUK PRODUK REGULER / PRODUK KARTU LAINNYA ---
            $productId = $request->product_id;
            if (!$productId) {
                return back()->with('error', 'Produk tidak ditemukan!');
            }
            $product = Product::findOrFail($productId);
            $qty     = (int) ($request->quantity ?? $request->qty ?? 1);
            
            $storeStock = StoreProductStock::where('store_id', $storeId)
                ->where('product_id', $product->id)
                ->first();
            $costPrice = ($storeStock && $storeStock->cost_price !== null) 
                ? (float) $storeStock->cost_price 
                : (float) $product->cost_price;
            $sellingPrice = ($storeStock && $storeStock->selling_price !== null) 
                ? (float) $storeStock->selling_price 
                : (float) $product->selling_price;
            
            $targetNum = $request->input('target_number') ?? $request->input('account_number') ?? $request->input('target_phone');
            if (empty(trim((string)$targetNum))) {
                $targetNum = '-';
            }
            $cartKey   = $product->id . ($targetNum !== '-' ? '_' . $targetNum : '');
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
            $defaultProvider = $request->input('digital_provider') ?? \App\Models\PpobServer::where('name', 'Propana')->value('name') ?? \App\Models\PpobServer::value('name') ?? 'Propana';
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
                    'cost_price'        => $costPrice,
                    'selling_price'     => $sellingPrice,
                    'qty'               => $qty,
                    'subtotal'          => (float) ($sellingPrice * $qty),
                    'profit'            => (float) (($sellingPrice - $costPrice) * $qty),
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

    /**
     * Memperbarui Provider / Jumlah di Keranjang
     */
    public function updateCart(Request $request, $key)
    {
        $storeId = $this->getActiveStoreId();
        $cart = session()->get('pos_cart', []);
        if (isset($cart[$key])) {
            if ($request->has('digital_provider')) {
                $newProvider = $request->input('digital_provider');
                $cart[$key]['digital_provider'] = $newProvider;

                $nameLower = strtolower($cart[$key]['name'] ?? '');
                if (str_contains($nameLower, 'transfer') || str_contains($nameLower, 'bank')) {
                    $customAmount = $cart[$key]['custom_amount'] ?? 0;
                    if ($customAmount > 0) {
                        $newCost = $this->calculateBankCostPrice($customAmount, $newProvider);
                        $cart[$key]['cost_price'] = $newCost;
                        $cart[$key]['profit']     = $cart[$key]['selling_price'] - $newCost;
                    }
                }
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
                    'product_id'        => $item['product_id'] ?? null,
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
                if ($productType === 'digital' && !empty($provider)) {
                    $ppobServer = PpobServer::whereRaw('LOWER(name) = ?', [strtolower(trim($provider))])->first();
                    
                    if ($ppobServer) {
                        $totalItemCost = (float) $item['cost_price'] * (int) $item['qty'];
                        $ppobServer->decrement('balance', $totalItemCost);
                    }
                }
                if ($productType !== 'digital' && !empty($item['product_id'])) {
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