<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
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
        // ----------------------------------------------------
        // 1. TOP-UP E-WALLET
        // ----------------------------------------------------
        $ewallets = [
            'DANA'      => ['slug' => 'ewallet-dana',      'code' => 'DANA'],
            'OVO'       => ['slug' => 'ewallet-ovo',       'code' => 'OVO'],
            'GoPay'     => ['slug' => 'ewallet-gopay',     'code' => 'GOPAY'],
            'ShopeePay' => ['slug' => 'ewallet-shopeepay', 'code' => 'SHOPEE'],
            'LinkAja'   => ['slug' => 'ewallet-linkaja',   'code' => 'LINKAJA'],
            'Maxim'     => ['slug' => 'ewallet-maxim',     'code' => 'MAXIM'],
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
            $catWallet = Category::where('slug', $walletData['slug'])->first();
            $catId = $catWallet ? $catWallet->id : Category::where('slug', 'topup-ewallet')->first()?->id;

            if ($catId) {
                foreach ($ewalletNominals as $k => $name) {
                    $costPrice    = $k * 1000; // Modal awal (Rp 10.000, dst)
                    $adminFee     = $this->calculateAdminFee($k);
                    $sellingPrice = $costPrice + $adminFee;

                    Product::create([
                        'category_id'   => $catId,
                        'name'          => "Top-Up {$walletName} {$name}",
                        'code'          => "{$walletData['code']}{$k}K",
                        'type'          => 'digital',
                        'cost_price'    => $costPrice,
                        'selling_price' => $sellingPrice,
                        'stock'         => 0,
                        'min_stock'     => 0,
                        'is_active'     => true,
                    ]);
                }
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
            $catBank = Category::where('slug', $bankData['slug'])->first();
            $catId = $catBank ? $catBank->id : Category::where('slug', 'transfer-bank')->first()?->id;

            if ($catId) {
                foreach ($bankNominals as $k => $name) {
                    $costPrice    = $k * 1000;
                    $adminFee     = $this->calculateAdminFee($k);
                    $sellingPrice = $costPrice + $adminFee;

                    Product::create([
                        'category_id'   => $catId,
                        'name'          => "Transfer {$bankName} {$name}",
                        'code'          => "TRF-{$bankData['code']}{$k}K",
                        'type'          => 'digital',
                        'cost_price'    => $costPrice,
                        'selling_price' => $sellingPrice,
                        'stock'         => 0,
                        'min_stock'     => 0,
                        'is_active'     => true,
                    ]);
                }
            }
        }

        // ----------------------------------------------------
        // 3. TOKEN PLN
        // ----------------------------------------------------
        $catPLN = Category::where('slug', 'token-pln')->first()?->id;
        if ($catPLN) {
            $plnTokens = [
                ['name' => 'Token PLN 20.000',  'code' => 'PLN20K',  'cost' => 20100,  'sell' => 22000],
                ['name' => 'Token PLN 50.000',  'code' => 'PLN50K',  'cost' => 50100,  'sell' => 52000],
                ['name' => 'Token PLN 100.000', 'code' => 'PLN100K', 'cost' => 100100, 'sell' => 103000],
                ['name' => 'Token PLN 200.000', 'code' => 'PLN200K', 'cost' => 200100, 'sell' => 203000],
            ];

            foreach ($plnTokens as $p) {
                Product::create([
                    'category_id'   => $catPLN,
                    'name'          => $p['name'],
                    'code'          => $p['code'],
                    'type'          => 'digital',
                    'cost_price'    => $p['cost'],
                    'selling_price' => $p['sell'],
                    'stock'         => 0,
                    'min_stock'     => 0,
                    'is_active'     => true,
                ]);
            }
        }
    }
}