<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class EwalletBankProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. TOP-UP E-WALLET
        $ewallets = [
            'DANA'      => ['slug' => 'ewallet-dana',      'code' => 'DANA'],
            'OVO'       => ['slug' => 'ewallet-ovo',       'code' => 'OVO'],
            'GoPay'     => ['slug' => 'ewallet-gopay',     'code' => 'GOPAY'],
            'ShopeePay' => ['slug' => 'ewallet-shopeepay', 'code' => 'SHOPEE'],
            'LinkAja'   => ['slug' => 'ewallet-linkaja',   'code' => 'LINKAJA'],
            'Maxim'     => ['slug' => 'ewallet-maxim',     'code' => 'MAXIM'],
        ];

        $ewalletNominals = [
            10  => ['name' => '10.000',  'cost' => 10500,  'sell' => 12500],
            20  => ['name' => '20.000',  'cost' => 20500,  'sell' => 22500],
            50  => ['name' => '50.000',  'cost' => 50500,  'sell' => 52500],
            100 => ['name' => '100.000', 'cost' => 100500, 'sell' => 102500],
        ];

        foreach ($ewallets as $walletName => $walletData) {
            $catWallet = Category::where('slug', $walletData['slug'])->first();
            $catId = $catWallet ? $catWallet->id : Category::where('slug', 'topup-ewallet')->first()?->id;

            if ($catId) {
                foreach ($ewalletNominals as $k => $e) {
                    Product::create([
                        'category_id'   => $catId,
                        'name'          => "Top-Up {$walletName} {$e['name']}",
                        'code'          => "{$walletData['code']}{$k}K",
                        'type'          => 'digital',
                        'cost_price'    => $e['cost'],
                        'selling_price' => $e['sell'],
                        'stock'         => 0,
                        'min_stock'     => 0,
                        'is_active'     => true,
                    ]);
                }
            }
        }

        // 2. TRANSFER BANK
        $banks = [
            'Bank BCA'     => ['slug' => 'bank-bca',     'code' => 'BCA'],
            'Bank BRI'     => ['slug' => 'bank-bri',     'code' => 'BRI'],
            'Bank Mandiri' => ['slug' => 'bank-mandiri', 'code' => 'MDR'],
            'Bank BNI'     => ['slug' => 'bank-bni',     'code' => 'BNI'],
            'Bank BSI'     => ['slug' => 'bank-bsi',     'code' => 'BSI'],
            'Bank Permata' => ['slug' => 'bank-permata', 'code' => 'PERMATA'],
        ];

        $bankNominals = [
            50  => ['name' => '50.000',  'cost' => 50000,  'sell' => 52500],
            100 => ['name' => '100.000', 'cost' => 100000, 'sell' => 102500],
            250 => ['name' => '250.000', 'cost' => 250000, 'sell' => 252500],
            500 => ['name' => '500.000', 'cost' => 500000, 'sell' => 503500],
        ];

        foreach ($banks as $bankName => $bankData) {
            $catBank = Category::where('slug', $bankData['slug'])->first();
            $catId = $catBank ? $catBank->id : Category::where('slug', 'transfer-bank')->first()?->id;

            if ($catId) {
                foreach ($bankNominals as $k => $b) {
                    Product::create([
                        'category_id'   => $catId,
                        'name'          => "Transfer {$bankName} {$b['name']}",
                        'code'          => "TRF-{$bankData['code']}{$k}K",
                        'type'          => 'digital',
                        'cost_price'    => $b['cost'],
                        'selling_price' => $b['sell'],
                        'stock'         => 0,
                        'min_stock'     => 0,
                        'is_active'     => true,
                    ]);
                }
            }
        }

        // 3. TOKEN PLN
        $catPLN = Category::where('slug', 'token-pln')->first()?->id;
        if ($catPLN) {
            $plnTokens = [
                ['name' => 'Token PLN 20.000',  'code' => 'PLN20K',  'cost' => 20100,  'sell' => 22500],
                ['name' => 'Token PLN 50.000',  'code' => 'PLN50K',  'cost' => 50100,  'sell' => 52500],
                ['name' => 'Token PLN 100.000', 'code' => 'PLN100K', 'cost' => 100100, 'sell' => 102500],
                ['name' => 'Token PLN 200.000', 'code' => 'PLN200K', 'cost' => 200100, 'sell' => 202500],
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