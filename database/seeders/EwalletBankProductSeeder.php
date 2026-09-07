<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProductStock;
use Illuminate\Database\Seeder;

class EwalletBankProductSeeder extends Seeder
{
    /**
     * Helper Function untuk Menghitung Biaya Admin E-Wallet / Bank
     */
    private function calculateAdminFee(int $nominalInThousand): float
    {
        $nominal = $nominalInThousand * 1000;

        if ($nominalInThousand < 100) {
            return 2000; // < 100rb = Admin 2.000
        } elseif ($nominalInThousand >= 100 && $nominalInThousand < 400) {
            return 3000; // 100rb - 399rb = Admin 3.000
        } else {
            return $nominal * 0.01; // >= 400rb = Admin 1% (400k -> 4k, 500k -> 5k, 1jt -> 10k)
        }
    }

    public function run(): void
    {
        // Helper function untuk mengambil/fallback category ID
        $getCatId = function ($slug, $fallbackSlug) {
            $cat = Category::where('slug', $slug)->first();
            return $cat ? $cat->id : Category::where('slug', $fallbackSlug)->first()?->id;
        };

        // ----------------------------------------------------
        // 1. TOP-UP E-WALLET (BIAYA PERAK MODAL SESUAI CATATAN)
        // ----------------------------------------------------
        $ewallets = [
            'DANA'      => ['slug' => 'ewallet-dana',      'code' => 'DANA',    'fee_perak' => 101],
            'GoPay'     => ['slug' => 'ewallet-gopay',     'code' => 'GOPAY',   'fee_perak' => 910],
            'ShopeePay' => ['slug' => 'ewallet-shopeepay', 'code' => 'SHOPEE',  'fee_perak' => 75],
            'OVO'       => ['slug' => 'ewallet-ovo',       'code' => 'OVO',     'fee_perak' => 631],
            'LinkAja'   => ['slug' => 'ewallet-linkaja',   'code' => 'LINKAJA', 'fee_perak' => 500],
            'Maxim'     => ['slug' => 'ewallet-maxim',     'code' => 'MAXIM',   'fee_perak' => 2700],
        ];

        // Daftar nominal e-wallet dalam ribuan (K)
        $ewalletNominals = [
            10   => '10.000',
            20   => '20.000',
            50   => '50.000',
            100  => '100.000',
            200  => '200.000',
            300  => '300.000',
            400  => '400.000',
            500  => '500.000',
            1000 => '1.000.000',
        ];

        foreach ($ewallets as $walletName => $walletData) {
            $catId = $getCatId($walletData['slug'], 'topup-ewallet');

            if ($catId) {
                $walletProducts = [];
                foreach ($ewalletNominals as $k => $name) {
                    $baseNominal  = $k * 1000;
                    $costPrice    = $baseNominal + $walletData['fee_perak']; // Modal = Nominal + Biaya Perak
                    
                    // Khusus Maxim nominal 10k - 100k, admin_fee fixed 4.000
                    if ($walletName === 'Maxim' && $k >= 10 && $k <= 100) {
                        $adminFee = 4000;
                    } else {
                        $adminFee = $this->calculateAdminFee($k);
                    }

                    $sellingPrice = $baseNominal + $adminFee;

                    $walletProducts[] = [
                        'name' => "Top-Up {$walletName} {$name}",
                        'code' => "{$walletData['code']}{$k}K",
                        'cost' => $costPrice,
                        'sell' => $sellingPrice,
                    ];
                }
                $this->seedGroup($catId, $walletProducts);
            }
        }

        // ----------------------------------------------------
        // 2. TRANSFER BANK
        // ----------------------------------------------------
        $banks = [
            'Bank BCA'     => ['slug' => 'bank-bca',     'code' => 'BCA'],
            'Bank BRI'     => ['slug' => 'bank-bri',     'code' => 'BRI'],
            'Bank Mandiri' => ['slug' => 'bank-mandiri', 'code' => 'MDR'],
            'Bank BNI'     => ['slug' => 'bank-bni',     'code' => 'BNI'],
            'Bank BSI'     => ['slug' => 'bank-bsi',     'code' => 'BSI'],
            'Bank Permata' => ['slug' => 'bank-permata', 'code' => 'PERMATA'],
        ];

        $bankNominals = [
            50   => '50.000',
            100  => '100.000',
            250  => '250.000',
            400  => '400.000',
            500  => '500.000',
            1000 => '1.000.000',
        ];

        foreach ($banks as $bankName => $bankData) {
            $catId = $getCatId($bankData['slug'], 'transfer-bank');

            if ($catId) {
                $bankProducts = [];
                foreach ($bankNominals as $k => $name) {
                    $costPrice    = $k * 1000;
                    $adminFee     = $this->calculateAdminFee($k);
                    $sellingPrice = $costPrice + $adminFee;

                    $bankProducts[] = [
                        'name' => "Transfer {$bankName} {$name}",
                        'code' => "TRF-{$bankData['code']}{$k}K",
                        'cost' => $costPrice,
                        'sell' => $sellingPrice,
                    ];
                }
                $this->seedGroup($catId, $bankProducts);
            }
        }

        // ----------------------------------------------------
        // 3. TOKEN PLN
        // ----------------------------------------------------
        $catPLN = Category::where('slug', 'token-pln')->first()?->id;
        if ($catPLN) {
            $plnTokens = [
                ['name' => 'Token PLN 20.000',     'code' => 'PLN20K',  'cost' => 21400,   'sell' => 22000],
                ['name' => 'Token PLN 25.000',     'code' => 'PLN25K',  'cost' => 26511,   'sell' => 27000],
                ['name' => 'Token PLN 50.000',     'code' => 'PLN50K',  'cost' => 51261,   'sell' => 52000],
                ['name' => 'Token PLN 100.000',    'code' => 'PLN100K', 'cost' => 101321,  'sell' => 103000],
                ['name' => 'Token PLN 200.000',    'code' => 'PLN200K', 'cost' => 201321,  'sell' => 203000],
                ['name' => 'Token PLN 500.000',    'code' => 'PLN500K', 'cost' => 501321,  'sell' => 503000],
                ['name' => 'Token PLN 1.000.000', 'code' => 'PLN1M',   'cost' => 1001321, 'sell' => 1003000],
            ];
            $this->seedGroup($catPLN, $plnTokens);
        }
    }

    private function seedGroup($catId, array $items)
    {
        foreach ($items as $item) {
            // 1. Buat Master Produk (Global) tipe Digital
            $product = Product::create([
                'category_id'   => $catId,
                'name'          => $item['name'],
                'code'          => $item['code'],
                'type'          => 'digital',
                'cost_price'    => $item['cost'],
                'selling_price' => $item['sell'],
                'stock'         => 0, // Fallback master untuk produk digital
                'min_stock'     => 0, // Fallback master untuk produk digital
                'is_active'     => true,
            ]);

            // 2. Alokasikan Stok & Min_Stock untuk Store 1 (WannCell)
            StoreProductStock::updateOrCreate(
                [
                    'store_id'   => 1,
                    'product_id' => $product->id,
                ],
                [
                    'stock'     => 0,
                    'min_stock' => 0,
                ]
            );

            // 3. Alokasikan Stok & Min_Stock untuk Store 2
            StoreProductStock::updateOrCreate(
                [
                    'store_id'   => 2,
                    'product_id' => $product->id,
                ],
                [
                    'stock'     => 0,
                    'min_stock' => 0,
                ]
            );
        }
    }
}