<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProductStock;
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
                ['name' => 'Voucher Telkomsel 6GB (2 Hari)',  'code' => 'V-TSEL-6GB-2H',   'cost' => 11000, 'sell' => 13000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 4GB (5 Hari)',  'code' => 'V-TSEL-4GB-5H',   'cost' => 13000, 'sell' => 16000, 'stock' => 15, 'min_stock' => 10],
                ['name' => 'Voucher Telkomsel 6GB (5 Hari)',  'code' => 'V-TSEL-6GB-5H',   'cost' => 15500, 'sell' => 18000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Telkomsel 5GB (7 Hari)',  'code' => 'V-TSEL-5GB-7H',   'cost' => 20200, 'sell' => 22000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 9GB (7 Hari)',  'code' => 'V-TSEL-9GB-7H',   'cost' => 28000, 'sell' => 30000, 'stock' => 5,  'min_stock' => 3],
                ['name' => 'Voucher Telkomsel 7GB (30 Hari)', 'code' => 'V-TSEL-7GB-30H',  'cost' => 33700, 'sell' => 37000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Telkomsel 10GB (30 Hari)','code' => 'V-TSEL-10GB-30H', 'cost' => 41600, 'sell' => 45000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 18GB (30 Hari)','code' => 'V-TSEL-18GB-30H', 'cost' => 51200, 'sell' => 55000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 26GB (30 Hari)','code' => 'V-TSEL-26GB-30H', 'cost' => 64200, 'sell' => 69000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Telkomsel 6/7 By.U',      'code' => 'V-BYU-6GB-7H',   'cost' => 13400, 'sell' => 19000, 'stock' => 2,  'min_stock' => 5],
                ['name' => 'Voucher Telkomsel 7GB By.U',      'code' => 'V-BYU-7GB-30H',  'cost' => 33400, 'sell' => 37000, 'stock' => 2,  'min_stock' => 5],
            ];
            $this->seedGroup($catIdTsel, $tselVouchers);
        }

        // ----------------------------------------------------
        // 2. SEEDER VOUCHER INDOSAT
        // ----------------------------------------------------
        $catIdIsat = $getCatId('voucher-indosat');
        if ($catIdIsat) {
            $isatVouchers = [
                ['name' => 'Voucher Indosat 5GB (2 Hari)',   'code' => 'V-ISAT-5GB-2H',   'cost' => 9500,   'sell' => 11000,  'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Indosat 2.5GB (5 Hari)', 'code' => 'V-ISAT-2.5GB-5H', 'cost' => 13400,  'sell' => 15000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 5GB (5 Hari)',   'code' => 'V-ISAT-5GB-5H',   'cost' => 17100,  'sell' => 19000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 7GB (7 Hari)',   'code' => 'V-ISAT-7GB-7H',   'cost' => 23000,  'sell' => 25000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 7GB (30 Hari)',  'code' => 'V-ISAT-7GB-30H',  'cost' => 32000,  'sell' => 35000,  'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Indosat 10GB (30 Hari)', 'code' => 'V-ISAT-10GB-30H', 'cost' => 36500,  'sell' => 40000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 16GB (30 Hari)', 'code' => 'V-ISAT-16GB-30H', 'cost' => 44500,  'sell' => 48000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Indosat 1GB Unlimited',  'code' => 'V-ISAT-1GB-UNLI', 'cost' => 36300,  'sell' => 39000,  'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Indosat 2GB Unlimited',  'code' => 'V-ISAT-2GB-UNLI', 'cost' => 57800,  'sell' => 61000,  'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 3GB Unlimited',  'code' => 'V-ISAT-3GB-UNLI', 'cost' => 80500,  'sell' => 85000,  'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 7GB Unlimited',  'code' => 'V-ISAT-7GB-UNLI', 'cost' => 102000, 'sell' => 107000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 10GB Unlimited', 'code' => 'V-ISAT-10GB-UNLI','cost' => 114000, 'sell' => 118000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Indosat 3.5GB (5 Hari)', 'code' => 'V-ISAT-3.5GB-5H', 'cost' => 15100,  'sell' => 17000,  'stock' => 5,  'min_stock' => 5],
            ];
            $this->seedGroup($catIdIsat, $isatVouchers);
        }

        // ----------------------------------------------------
        // 3. SEEDER VOUCHER TRI (THREE)
        // ----------------------------------------------------
        $catIdTri = $getCatId('voucher-tri');
        if ($catIdTri) {
            $triVouchers = [
                ['name' => 'Voucher Tri 2GB (1 Hari)',   'code' => 'V-3-2GB-1H',   'cost' => 8000,  'sell' => 10000,  'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 7GB (2 Hari)',   'code' => 'V-3-7GB-2H',   'cost' => 10200, 'sell' => 14000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 5GB (3 Hari)',   'code' => 'V-3-5GB-3H',   'cost' => 12000,  'sell' => 16000, 'stock' => 15, 'min_stock' => 15],
                ['name' => 'Voucher Tri 10GB (3 Hari)',  'code' => 'V-3-10GB-3H',  'cost' => 13000, 'sell' => 20000, 'stock' => 3,  'min_stock' => 5],
                ['name' => 'Voucher Tri 10GB (5 Hari)',  'code' => 'V-3-10GB-5H',  'cost' => 17300, 'sell' => 23000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 11GB (5 Hari)',  'code' => 'V-3-11GB-5H',  'cost' => 20300, 'sell' => 23000, 'stock' => 3,  'min_stock' => 4],
                ['name' => 'Voucher Tri 15GB (7 Hari)',  'code' => 'V-3-15GB-7H',  'cost' => 22700, 'sell' => 25000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 4GB AON',        'code' => 'V-3-4GB-AON',  'cost' => 24800, 'sell' => 28000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Tri 6GB AON',        'code' => 'V-3-6GB-AON',  'cost' => 30900, 'sell' => 35000, 'stock' => 5,  'min_stock' => 8],
                ['name' => 'Voucher Tri 7GB Happy (30H)','code' => 'V-3-7GB-HAPPY','cost' => 31200, 'sell' => 34000, 'stock' => 5,  'min_stock' => 10],
                ['name' => 'Voucher Tri 9GB Happy (30H)','code' => 'V-3-9GB-HAPPY','cost' => 33500, 'sell' => 37000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 9GB AON',        'code' => 'V-3-9GB-AON',  'cost' => 46000, 'sell' => 50000, 'stock' => 2,  'min_stock' => 3],
                ['name' => 'Voucher Tri 12GB AON',       'code' => 'V-3-12GB-AON', 'cost' => 58000, 'sell' => 62000, 'stock' => 2,  'min_stock' => 3],
                ['name' => 'Voucher Tri 16GB Happy (30H)','code' => 'V-3-16GB-HAPPY','cost' => 44400, 'sell' => 49000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher Tri 42GB (30 Hari)',  'code' => 'V-3-42GB-30H', 'cost' => 73900, 'sell' => 78000, 'stock' => 5,  'min_stock' => 5],
            ];
            $this->seedGroup($catIdTri, $triVouchers);
        }

        // ----------------------------------------------------
        // 4. SEEDER VOUCHER SMARTFREN
        // ----------------------------------------------------
        $catIdSmart = $getCatId('voucher-smartfren');
        if ($catIdSmart) {
            $smartVouchers = [
                ['name' => 'Voucher Smartfren 1GB (3 Hari)',     'code' => 'V-SMART-1GB-3H',    'cost' => 6600,  'sell' => 8000,  'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 4GB (3 Hari)',     'code' => 'V-SMART-4GB-3H',    'cost' => 9800,  'sell' => 11000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 2GB (7 Hari)',     'code' => 'V-SMART-2GB-7H',    'cost' => 10000, 'sell' => 12000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 2GB (10 Hari)',    'code' => 'V-SMART-2GB-10H',   'cost' => 12900, 'sell' => 15000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 4GB (5 Hari)',     'code' => 'V-SMART-4GB-5H',    'cost' => 14000, 'sell' => 16000, 'stock' => 3, 'min_stock' => 4],
                ['name' => 'Voucher Smartfren 6GB (7 Hari)',     'code' => 'V-SMART-6GB-7H',    'cost' => 15700, 'sell' => 18000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 5GB (14 Hari)',    'code' => 'V-SMART-5GB-14H',   'cost' => 20000, 'sell' => 22000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 10GB (7 Hari)',    'code' => 'V-SMART-10GB-7H',   'cost' => 19000, 'sell' => 22000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 15GB (10 Hari)',   'code' => 'V-SMART-15GB-10H',  'cost' => 30000, 'sell' => 35000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 6GB (30 Hari)',    'code' => 'V-SMART-6GB-30H',   'cost' => 41500, 'sell' => 45000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 7GB (30 Hari)',    'code' => 'V-SMART-7GB-30H',   'cost' => 32000, 'sell' => 35000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren 10GB (30 Hari)',   'code' => 'V-SMART-10GB-30H',  'cost' => 38800, 'sell' => 43000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 24GB (30 Hari)',   'code' => 'V-SMART-24GB-30H',  'cost' => 55500, 'sell' => 60000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren 40GB (30 Hari)',   'code' => 'V-SMART-40GB-30H',  'cost' => 74000, 'sell' => 80000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 1GB (7 Hari)','code' => 'V-SMART-UNLI-1GB',  'cost' => 17400, 'sell' => 20000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Voucher Smartfren Unli 3GB (7 Hari)','code' => 'V-SMART-UNLI-3GB',  'cost' => 30500, 'sell' => 33000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 1GB Lite',    'code' => 'V-SMART-UNLI-LITE', 'cost' => 63500, 'sell' => 76000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 2GB (28 Hari)','code' => 'V-SMART-UNLI-28H',  'cost' => 89800, 'sell' => 95000, 'stock' => 3, 'min_stock' => 3],
                ['name' => 'Voucher Smartfren Unli 2GB (7 Hari)', 'code' => 'V-SMART-UNLI-2GB',  'cost' => 23200, 'sell' => 26000, 'stock' => 3, 'min_stock' => 3],
            ];
            $this->seedGroup($catIdSmart, $smartVouchers);
        }

        // ----------------------------------------------------
        // 5. SEEDER VOUCHER XL
        // ----------------------------------------------------
        $catIdXL = $getCatId('voucher-xl');
        if ($catIdXL) {
            $xlVouchers = [
                ['name' => 'Voucher XL 4GB (3 Hari)',   'code' => 'V-XL-4GB-3H',   'cost' => 12400, 'sell' => 14000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 6GB (2 Hari)',   'code' => 'V-XL-6GB-2H',   'cost' => 10500, 'sell' => 13000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 5GB (5 Hari)',   'code' => 'V-XL-5GB-5H',   'cost' => 14500, 'sell' => 17000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 7GB (7 Hari)',   'code' => 'V-XL-7GB-7H',   'cost' => 19000, 'sell' => 22000, 'stock' => 5,  'min_stock' => 8],
                ['name' => 'Voucher XL 4GB (14 Hari)',  'code' => 'V-XL-4GB-14H',  'cost' => 19500, 'sell' => 22000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 7GB (30 Hari)',  'code' => 'V-XL-7GB-30H',  'cost' => 32300, 'sell' => 35000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher XL 10GB (30 Hari)', 'code' => 'V-XL-10GB-30H', 'cost' => 37500, 'sell' => 41000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 16GB (30 Hari)', 'code' => 'V-XL-16GB-30H', 'cost' => 45700, 'sell' => 50000, 'stock' => 5,  'min_stock' => 5],
                ['name' => 'Voucher XL 23GB (30 Hari)', 'code' => 'V-XL-23GB-30H', 'cost' => 55000, 'sell' => 60000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher XL 31GB (30 Hari)', 'code' => 'V-XL-31GB-30H', 'cost' => 63400, 'sell' => 69000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher XL 40GB (30 Hari)', 'code' => 'V-XL-40GB-30H', 'cost' => 71400, 'sell' => 78000, 'stock' => 2,  'min_stock' => 2],
            ];
            $this->seedGroup($catIdXL, $xlVouchers);
        }

        // ----------------------------------------------------
        // 6. SEEDER VOUCHER AXIS
        // ----------------------------------------------------
        $catIdAxis = $getCatId('voucher-axis');
        if ($catIdAxis) {
            $axisVouchers = [
                ['name' => 'Voucher Axis 5GB (2 Hari)',   'code' => 'V-AXIS-5GB-2H',   'cost' => 9500, 'sell' => 11000, 'stock' => 10, 'min_stock' => 10],
                ['name' => 'Voucher Axis 5GB (3 Hari)',   'code' => 'V-AXIS-5GB-3H',   'cost' => 11700, 'sell' => 13000, 'stock' => 15, 'min_stock' => 10],
                ['name' => 'Voucher Axis 5GB (5 Hari)',   'code' => 'V-AXIS-5GB-5H',   'cost' => 13700, 'sell' => 17000, 'stock' => 15, 'min_stock' => 10],
                ['name' => 'Voucher Axis 11GB (7 Hari)',  'code' => 'V-AXIS-11GB-7H',  'cost' => 22900, 'sell' => 25000, 'stock' => 3,  'min_stock' => 5],
                ['name' => 'Voucher Axis 8GB (15 Hari)',  'code' => 'V-AXIS-8GB-15H',  'cost' => 27400, 'sell' => 30000, 'stock' => 3,  'min_stock' => 4],
                ['name' => 'Voucher Axis 25GB (15 Hari)', 'code' => 'V-AXIS-25GB-15H', 'cost' => 44800, 'sell' => 50000, 'stock' => 2,  'min_stock' => 2],
                ['name' => 'Voucher Axis 7GB (30 Hari)',  'code' => 'V-AXIS-7GB-30H',  'cost' => 32700, 'sell' => 39000, 'stock' => 5,  'min_stock' => 8],
                ['name' => 'Voucher Axis 10GB (30 Hari)', 'code' => 'V-AXIS-10GB-30H', 'cost' => 37700, 'sell' => 41000, 'stock' => 3,  'min_stock' => 3],
                ['name' => 'Voucher Axis 16GB (30 Hari)', 'code' => 'V-AXIS-16GB-30H', 'cost' => 45800, 'sell' => 50000, 'stock' => 2,  'min_stock' => 3],
            ];
            $this->seedGroup($catIdAxis, $axisVouchers);
        }
    }

    private function seedGroup($catId, array $vouchers)
    {
        foreach ($vouchers as $v) {
            // 1. Buat Master Produk (Global)
            $product = Product::create([
                'category_id'   => $catId,
                'name'          => $v['name'],
                'code'          => $v['code'],
                'type'          => 'physical',
                'cost_price'    => $v['cost'],
                'selling_price' => $v['sell'],
                'stock'         => $v['stock'],     // Master fallback
                'min_stock'     => $v['min_stock'], // Master fallback
                'is_active'     => true,
            ]);

            // 2. Alokasikan Stok & Min_Stock untuk Store 1 (WannCell)
            StoreProductStock::updateOrCreate(
                [
                    'store_id'   => 1,
                    'product_id' => $product->id,
                ],
                [
                    'stock'     => $v['stock'],
                    'min_stock' => $v['min_stock'],
                ]
            );

            StoreProductStock::updateOrCreate(
                [
                    'store_id'   => 2,
                    'product_id' => $product->id,
                ],
                [
                    'stock'     => $v['stock'],
                    'min_stock' => $v['min_stock'],
                ]
            );
        }
    }
}