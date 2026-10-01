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
        $pulsaRoot = Category::updateOrCreate(
            ['slug' => 'pulsa-reguler'],
            [
                'name'      => 'Pulsa Reguler',
                'parent_id' => null,
            ]
        );
        foreach ($operators as $op) {
            Category::updateOrCreate(
                ['slug' => 'pulsa-' . Str::slug($op)],
                [
                    'name'      => $op,
                    'parent_id' => $pulsaRoot->id,
                ]
            );
        }

        // 2. VOUCHER INTERNET & SUB-KATEGORI PROVIDER
        $voucherRoot = Category::updateOrCreate(
            ['slug' => 'voucher-internet'],
            [
                'name'      => 'Voucher Internet',
                'parent_id' => null,
            ]
        );
        foreach ($operators as $op) {
            Category::updateOrCreate(
                ['slug' => 'voucher-' . Str::slug($op)],
                [
                    'name'      => "Voucher {$op}",
                    'parent_id' => $voucherRoot->id,
                ]
            );
        }

        // 3. KARTU PERDANA & SUB-KATEGORI PROVIDER
        $perdanaRoot = Category::updateOrCreate(
            ['slug' => 'kartu-perdana'],
            [
                'name'      => 'Kartu Perdana',
                'parent_id' => null,
            ]
        );
        foreach ($operators as $op) {
            Category::updateOrCreate(
                ['slug' => 'perdana-' . Str::slug($op)],
                [
                    'name'      => "Perdana {$op}",
                    'parent_id' => $perdanaRoot->id,
                ]
            );
        }

        // 4. HANDPHONE (BARU & SECOND - SESUAI POS)
        $hpCategory = Category::updateOrCreate(
            ['slug' => 'handphone'],
            [
                'name'      => 'Handphone',
                'parent_id' => null,
            ]
        );
        Category::updateOrCreate(
            ['slug' => 'hp-new'],
            [
                'name'      => 'Handphone Baru (New)',
                'parent_id' => $hpCategory->id,
            ]
        );
        Category::updateOrCreate(
            ['slug' => 'hp-second'],
            [
                'name'      => 'Handphone Second',
                'parent_id' => $hpCategory->id,
            ]
        );

        // 5. TOP-UP E-WALLET
        $ewalletRoot = Category::updateOrCreate(
            ['slug' => 'topup-ewallet'],
            [
                'name'      => 'Top-Up E-Wallet',
                'parent_id' => null,
            ]
        );
        $ewallets = ['DANA', 'OVO', 'GoPay', 'ShopeePay', 'LinkAja', 'Maxim'];
        foreach ($ewallets as $wallet) {
            Category::updateOrCreate(
                ['slug' => 'ewallet-' . Str::slug($wallet)],
                [
                    'name'      => $wallet,
                    'parent_id' => $ewalletRoot->id,
                ]
            );
        }

        // 6. TRANSFER BANK
        $bankRoot = Category::updateOrCreate(
            ['slug' => 'transfer-bank'],
            [
                'name'      => 'Transfer Bank',
                'parent_id' => null,
            ]
        );
        $banks = ['BCA', 'BRI', 'Mandiri', 'BNI', 'BSI', 'Permata', 'SeaBank', 'Bank Jago', 'CIMB Niaga', 'Danamon', 'BTN', 'Bank Daerah (BPD)', 'Bank Lainnya'];
        foreach ($banks as $bank) {
            Category::updateOrCreate(
                ['slug' => 'bank-' . Str::slug($bank)],
                [
                    'name'      => "Bank {$bank}",
                    'parent_id' => $bankRoot->id,
                ]
            );
        }

        // 7. TOKEN PLN
        Category::updateOrCreate(
            ['slug' => 'token-pln'],
            [
                'name'      => 'Token PLN',
                'parent_id' => null,
            ]
        );

        // 8. AKSESORIS HP (9 SUB-KATEGORI SESUAI PERSIS POS sub-providers.blade.php)
        $accRoot = Category::updateOrCreate(
            ['slug' => 'aksesoris-hp'],
            [
                'name'      => 'Aksesoris HP',
                'parent_id' => null,
            ]
        );

        $accessories = [
            ['name' => 'Cable Data & AUX', 'slug' => 'cable-data-aux'],
            ['name' => 'Adaptor / Kepala', 'slug' => 'adaptor-kepala'],
            ['name' => 'Adaptor 1Set & Car', 'slug' => 'adaptor-set-car'],
            ['name' => 'Headset / Earphone', 'slug' => 'headset-earphone'],
            ['name' => 'MMC & Flashdisk', 'slug' => 'mmc-flashdisk'],
            ['name' => 'Powerbank', 'slug' => 'powerbank'],
            ['name' => 'Softcase', 'slug' => 'softcase'],
            ['name' => 'Antigores', 'slug' => 'antigores'],
            ['name' => 'ACC Mix', 'slug' => 'acc-mix'],
        ];

        foreach ($accessories as $acc) {
            Category::updateOrCreate(
                ['slug' => $acc['slug']],
                [
                    'name'      => $acc['name'],
                    'parent_id' => $accRoot->id,
                ]
            );
        }
    }
}