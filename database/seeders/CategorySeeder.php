<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $operators = ['Telkomsel', 'Indosat', 'XL', 'Tri', 'Axis', 'Smartfren'];

        // 1. PULSA REGULER & SUB-KATEGORI PROVIDER
        $pulsaRoot = Category::create([
            'name'      => 'Pulsa Reguler',
            'slug'      => 'pulsa-reguler',
            'parent_id' => null,
        ]);
        foreach ($operators as $op) {
            Category::create([
                'name'      => $op,
                'slug'      => 'pulsa-' . Str::slug($op),
                'parent_id' => $pulsaRoot->id,
            ]);
        }

        // 2. VOUCHER INTERNET & SUB-KATEGORI PROVIDER
        $voucherRoot = Category::create([
            'name'      => 'Voucher Internet',
            'slug'      => 'voucher-internet',
            'parent_id' => null,
        ]);
        foreach ($operators as $op) {
            Category::create([
                'name'      => "Voucher {$op}",
                'slug'      => 'voucher-' . Str::slug($op),
                'parent_id' => $voucherRoot->id,
            ]);
        }

        // 3. KARTU PERDANA & SUB-KATEGORI PROVIDER
        $perdanaRoot = Category::create([
            'name'      => 'Kartu Perdana',
            'slug'      => 'kartu-perdana',
            'parent_id' => null,
        ]);
        foreach ($operators as $op) {
            Category::create([
                'name'      => "Perdana {$op}",
                'slug'      => 'perdana-' . Str::slug($op),
                'parent_id' => $perdanaRoot->id,
            ]);
        }

        // 4. HANDPHONE (BARU & SECOND)
        $hpCategory = Category::create([
            'name'      => 'Handphone',
            'slug'      => 'handphone',
            'parent_id' => null,
        ]);
        Category::create([
            'name'      => 'Baru (New)',
            'slug'      => 'hp-baru',
            'parent_id' => $hpCategory->id,
        ]);
        Category::create([
            'name'      => 'Second / Bekas',
            'slug'      => 'hp-second',
            'parent_id' => $hpCategory->id,
        ]);

        // 5. TOP-UP E-WALLET
        $ewalletRoot = Category::create([
            'name'      => 'Top-Up E-Wallet',
            'slug'      => 'topup-ewallet',
            'parent_id' => null,
        ]);
        $ewallets = ['DANA', 'OVO', 'GoPay', 'ShopeePay', 'LinkAja', 'Maxim'];
        foreach ($ewallets as $wallet) {
            Category::create([
                'name'      => $wallet,
                'slug'      => 'ewallet-' . Str::slug($wallet),
                'parent_id' => $ewalletRoot->id,
            ]);
        }

        // 6. TRANSFER BANK
        $bankRoot = Category::create([
            'name'      => 'Transfer Bank',
            'slug'      => 'transfer-bank',
            'parent_id' => null,
        ]);
        $banks = ['BCA', 'BRI', 'Mandiri', 'BNI', 'BSI', 'Permata'];
        foreach ($banks as $bank) {
            Category::create([
                'name'      => "Bank {$bank}",
                'slug'      => 'bank-' . Str::slug($bank),
                'parent_id' => $bankRoot->id,
            ]);
        }

        // 7. TOKEN PLN
        Category::create([
            'name'      => 'Token PLN',
            'slug'      => 'token-pln',
            'parent_id' => null,
        ]);

        // 8. AKSESORIS HP
        $accRoot = Category::create([
            'name'      => 'Aksesoris HP',
            'slug'      => 'aksesoris-hp',
            'parent_id' => null,
        ]);
        $accessories = ['Proteksi', 'Power', 'Audio', 'Penyimpanan', 'Mount & Stand'];
        foreach ($accessories as $acc) {
            Category::create([
                'name'      => $acc,
                'slug'      => 'acc-' . Str::slug($acc),
                'parent_id' => $accRoot->id,
            ]);
        }
    }
}