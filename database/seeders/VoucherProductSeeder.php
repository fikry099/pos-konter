<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class VoucherProductSeeder extends Seeder
{
    public function run(): void
    {
        // Helper function untuk mengambil/fallback category ID
        $getCatId = function ($slug) {
            $cat = Category::where('slug', $slug)->first();
            return $cat ? $cat->id : Category::where('slug', 'voucher-internet')->first()?->id;
        };

        // ----------------------------------------------------
        // 1. SEEDER VOUCHER TELKOMSEL
        // ----------------------------------------------------
        $catIdTsel = $getCatId('voucher-telkomsel');
        if ($catIdTsel) {
            $tselVouchers = [
                ['name' => 'Voucher Telkomsel 6GB (2 Hari)',  'code' => 'V-TSEL-6GB-2H',  'cost' => 11000, 'sell' => 13000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 4GB (5 Hari)',  'code' => 'V-TSEL-4GB-5H',  'cost' => 14000, 'sell' => 16500, 'stock' => 15, 'min_stock' => 15],
                ['name' => 'Voucher Telkomsel 6GB (5 Hari)',  'code' => 'V-TSEL-6GB-5H',  'cost' => 17000, 'sell' => 20000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Telkomsel 5GB (7 Hari)',  'code' => 'V-TSEL-5GB-7H',  'cost' => 18000, 'sell' => 21000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 7GB (30 Hari)', 'code' => 'V-TSEL-7GB-30H', 'cost' => 28000, 'sell' => 33000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Telkomsel 10GB (30 Hari)','code' => 'V-TSEL-10GB-30H','cost' => 38000, 'sell' => 43000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 18GB (30 Hari)','code' => 'V-TSEL-18GB-30H','cost' => 58000, 'sell' => 65000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 26GB (30 Hari)','code' => 'V-TSEL-26GB-30H','cost' => 78000, 'sell' => 85000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Telkomsel 6/7 By.U',      'code' => 'V-BYU-6GB-7H',   'cost' => 17000, 'sell' => 20000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Telkomsel 7GB By.U',      'code' => 'V-BYU-7GB-30H',  'cost' => 27000, 'sell' => 31000, 'stock' => 2,  'min_stock' => 2],
            ];
            $this->seedGroup($catIdTsel, $tselVouchers);
        }

        // ----------------------------------------------------
        // 2. SEEDER VOUCHER INDOSAT
        // ----------------------------------------------------
        $catIdIsat = $getCatId('voucher-indosat');
        if ($catIdIsat) {
            $isatVouchers = [
                ['name' => 'Voucher Indosat 5GB (2 Hari)',   'code' => 'V-ISAT-5GB-2H',   'cost' => 10000, 'sell' => 12000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Indosat 2.5GB (5 Hari)', 'code' => 'V-ISAT-2.5GB-5H', 'cost' => 9000,  'sell' => 11000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 3.5GB (5 Hari)', 'code' => 'V-ISAT-3.5GB-5H', 'cost' => 11000, 'sell' => 13000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 5GB (5 Hari)',   'code' => 'V-ISAT-5GB-5H',   'cost' => 13500, 'sell' => 16000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 7GB (7 Hari)',   'code' => 'V-ISAT-7GB-7H',   'cost' => 18000, 'sell' => 21000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 7GB (30 Hari)',  'code' => 'V-ISAT-7GB-30H',  'cost' => 25000, 'sell' => 29000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Indosat 10GB (30 Hari)', 'code' => 'V-ISAT-10GB-30H', 'cost' => 33000, 'sell' => 38000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 16GB (30 Hari)', 'code' => 'V-ISAT-16GB-30H', 'cost' => 45000, 'sell' => 51000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 1GB Unlimited',  'code' => 'V-ISAT-1GB-UNLI', 'cost' => 15000, 'sell' => 18000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Indosat 2GB Unlimited',  'code' => 'V-ISAT-2GB-UNLI', 'cost' => 22000, 'sell' => 26000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 3GB Unlimited',  'code' => 'V-ISAT-3GB-UNLI', 'cost' => 28000, 'sell' => 33000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 7GB Unlimited',  'code' => 'V-ISAT-7GB-UNLI', 'cost' => 45000, 'sell' => 52000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 10GB Unlimited', 'code' => 'V-ISAT-10GB-UNLI','cost' => 58000, 'sell' => 65000, 'stock' => 2,  'min_stock' => 2],
            ];
            $this->seedGroup($catIdIsat, $isatVouchers);
        }

        // ----------------------------------------------------
        // 3. SEEDER VOUCHER TRI (THREE)
        // ----------------------------------------------------
        $catIdTri = $getCatId('voucher-tri');
        if ($catIdTri) {
            $triVouchers = [
                ['name' => 'Voucher Tri 6GB (1 Hari)',   'code' => 'V-3-6GB-1H',   'cost' => 7000,  'sell' => 9000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 7GB (2 Hari)',   'code' => 'V-3-7GB-2H',   'cost' => 10000, 'sell' => 12000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 3GB (3 Hari)',   'code' => 'V-3-3GB-3H',   'cost' => 8500,  'sell' => 10500, 'stock' => 15, 'min_stock' => 15],
                ['name' => 'Voucher Tri 10GB (3 Hari)',  'code' => 'V-3-10GB-3H',  'cost' => 14000, 'sell' => 17000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Tri 10GB (5 Hari)',  'code' => 'V-3-10GB-5H',  'cost' => 17000, 'sell' => 20000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 11GB (5 Hari)',  'code' => 'V-3-11GB-5H',  'cost' => 18500, 'sell' => 22000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Tri 15GB (7 Hari)',  'code' => 'V-3-15GB-7H',  'cost' => 23000, 'sell' => 27000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 4GB AON',         'code' => 'V-3-4GB-AON',  'cost' => 16000, 'sell' => 19000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 6GB AON',         'code' => 'V-3-6GB-AON',  'cost' => 22000, 'sell' => 26000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 7GB Happy (30H)', 'code' => 'V-3-7GB-HAPPY','cost' => 23000, 'sell' => 27000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 9GB Happy (30H)', 'code' => 'V-3-9GB-HAPPY','cost' => 27000, 'sell' => 31000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 9GB AON',         'code' => 'V-3-9GB-AON',  'cost' => 30000, 'sell' => 35000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Tri 16GB Happy (30H)','code' => 'V-3-16GB-HAPPY','cost' => 42000, 'sell' => 48000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 42GB (30 Hari)',  'code' => 'V-3-42GB-30H', 'cost' => 75000, 'sell' => 85000, 'stock' => 5,  'min_stock' => 5],
            ];
            $this->seedGroup($catIdTri, $triVouchers);
        }

        // ----------------------------------------------------
        // 4. SEEDER VOUCHER SMARTFREN
        // ----------------------------------------------------
        $catIdSmart = $getCatId('voucher-smartfren');
        if ($catIdSmart) {
            $smartVouchers = [
                ['name' => 'Voucher Smartfren 1GB (3 Hari)',     'code' => 'V-SMART-1GB-3H',  'cost' => 4500,  'sell' => 6000,  'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 4GB (3 Hari)',     'code' => 'V-SMART-4GB-3H',  'cost' => 9000,  'sell' => 11000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 2GB (7 Hari)',     'code' => 'V-SMART-2GB-7H',  'cost' => 8000,  'sell' => 10000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 2GB (10 Hari)',    'code' => 'V-SMART-2GB-10H', 'cost' => 9500,  'sell' => 12000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 4GB (5 Hari)',     'code' => 'V-SMART-4GB-5H',  'cost' => 11000, 'sell' => 13500, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 6GB (7 Hari)',     'code' => 'V-SMART-6GB-7H',  'cost' => 15000, 'sell' => 18000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 5GB (14 Hari)',    'code' => 'V-SMART-5GB-14H', 'cost' => 16000, 'sell' => 19000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 10GB (7 Hari)',    'code' => 'V-SMART-10GB-7H', 'cost' => 20000, 'sell' => 24000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 15GB (10 Hari)',   'code' => 'V-SMART-15GB-10H','cost' => 28000, 'sell' => 33000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 6GB (30 Hari)',    'code' => 'V-SMART-6GB-30H', 'cost' => 22000, 'sell' => 26000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 7GB (30 Hari)',    'code' => 'V-SMART-7GB-30H', 'cost' => 25000, 'sell' => 29000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 10GB (30 Hari)',   'code' => 'V-SMART-10GB-30H','cost' => 33000, 'sell' => 38000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 24GB (30 Hari)',   'code' => 'V-SMART-24GB-30H','cost' => 55000, 'sell' => 62000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 40GB (30 Hari)',   'code' => 'V-SMART-40GB-30H','cost' => 80000, 'sell' => 90000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 2GB (7 Hari)','code' => 'V-SMART-UNLI-2GB','cost' => 15000, 'sell' => 18000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren Unli 3GB (7 Hari)','code' => 'V-SMART-UNLI-3GB','cost' => 20000, 'sell' => 24000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 1GB Lite',    'code' => 'V-SMART-UNLI-LITE','cost' => 45000,'sell' => 52000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 2GB (28 Hari)','code'=>'V-SMART-UNLI-28H', 'cost' => 70000, 'sell' => 80000, 'stock' => 3, 'min_stock' => 3],
            ];
            $this->seedGroup($catIdSmart, $smartVouchers);
        }

        // ----------------------------------------------------
        // 5. SEEDER VOUCHER XL
        // ----------------------------------------------------
        $catIdXL = $getCatId('voucher-xl');
        if ($catIdXL) {
            $xlVouchers = [
                ['name' => 'Voucher XL 4GB (3 Hari)',   'code' => 'V-XL-4GB-3H',   'cost' => 10000, 'sell' => 12000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 6GB (2 Hari)',   'code' => 'V-XL-6GB-2H',   'cost' => 12000, 'sell' => 14000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 5GB (5 Hari)',   'code' => 'V-XL-5GB-5H',   'cost' => 13000, 'sell' => 15000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 7GB (7 Hari)',   'code' => 'V-XL-7GB-7H',   'cost' => 17000, 'sell' => 20000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 4GB (14 Hari)',  'code' => 'V-XL-4GB-14H',  'cost' => 16000, 'sell' => 19000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 7GB (30 Hari)',  'code' => 'V-XL-7GB-30H',  'cost' => 24000, 'sell' => 28000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 10GB (30 Hari)', 'code' => 'V-XL-10GB-30H', 'cost' => 32000, 'sell' => 37000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 16GB (30 Hari)', 'code' => 'V-XL-16GB-30H', 'cost' => 45000, 'sell' => 52000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 23GB (30 Hari)', 'code' => 'V-XL-23GB-30H', 'cost' => 60000, 'sell' => 68000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher XL 31GB (30 Hari)', 'code' => 'V-XL-31GB-30H', 'cost' => 75000, 'sell' => 85000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher XL 40GB (30 Hari)', 'code' => 'V-XL-40GB-30H', 'cost' => 92000, 'sell' => 102000,'stock' => 2,  'min_stock' => 2],
            ];
            $this->seedGroup($catIdXL, $xlVouchers);
        }

        // ----------------------------------------------------
        // 6. SEEDER VOUCHER AXIS
        // ----------------------------------------------------
        $catIdAxis = $getCatId('voucher-axis');
        if ($catIdAxis) {
            $axisVouchers = [
                ['name' => 'Voucher Axis 5GB (2 Hari)',   'code' => 'V-AXIS-5GB-2H',   'cost' => 10000, 'sell' => 12000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Axis 5GB (3 Hari)',   'code' => 'V-AXIS-5GB-3H',   'cost' => 12000, 'sell' => 14000, 'stock' => 15, 'min_stock' => 15],
                ['name' => 'Voucher Axis 4GB (5 Hari)',   'code' => 'V-AXIS-4GB-5H',   'cost' => 13000, 'sell' => 15000, 'stock' => 15, 'min_stock' => 15],
                ['name' => 'Voucher Axis 10GB (7 Hari)',  'code' => 'V-AXIS-10GB-7H',  'cost' => 20000, 'sell' => 24000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Axis 8GB (14 Hari)',  'code' => 'V-AXIS-8GB-14H',  'cost' => 22000, 'sell' => 26000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Axis 25GB (14 Hari)', 'code' => 'V-AXIS-25GB-14H', 'cost' => 40000, 'sell' => 46000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Axis 7GB (30 Hari)',  'code' => 'V-AXIS-7GB-30H',  'cost' => 25000, 'sell' => 29000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Axis 10GB (30 Hari)', 'code' => 'V-AXIS-10GB-30H', 'cost' => 33000, 'sell' => 38000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Axis 16GB (30 Hari)', 'code' => 'V-AXIS-16GB-30H', 'cost' => 46000, 'sell' => 52000, 'stock' => 2,  'min_stock' => 2],
            ];
            $this->seedGroup($catIdAxis, $axisVouchers);
        }
    }

    private function seedGroup($catId, array $vouchers)
    {
        foreach ($vouchers as $v) {
            Product::create([
                'category_id'   => $catId,
                'name'          => $v['name'],
                'code'          => $v['code'],
                'type'          => 'physical',
                'cost_price'    => $v['cost'],
                'selling_price' => $v['sell'],
                'stock'         => $v['stock'],
                'min_stock'     => $v['min_stock'],
                'is_active'     => true,
            ]);
        }
    }
}