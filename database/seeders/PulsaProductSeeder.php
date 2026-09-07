<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProductStock;
use Illuminate\Database\Seeder;

class PulsaProductSeeder extends Seeder
{
    public function run(): void
    {
        // Helper function untuk mengambil/fallback category ID
        $getCatId = function ($slug) {
            $cat = Category::where('slug', $slug)->first();
            return $cat ? $cat->id : Category::where('slug', 'pulsa')->first()?->id;
        };

        // ----------------------------------------------------
        // 1. SEEDER PULSA TELKOMSEL
        // ----------------------------------------------------
        $catIdTsel = $getCatId('pulsa-telkomsel');
        if ($catIdTsel) {
            $tselPulsa = [
                ['name' => 'Pulsa Telkomsel 5.000',   'code' => 'P-TSEL-5K',   'cost' => 5850,   'sell' => 7000], 
                ['name' => 'Pulsa Telkomsel 10.000',  'code' => 'P-TSEL-10K',  'cost' => 10850,  'sell' => 12000],
                ['name' => 'Pulsa Telkomsel 15.000',  'code' => 'P-TSEL-15K',  'cost' => 15850,  'sell' => 17000],
                ['name' => 'Pulsa Telkomsel 20.000',  'code' => 'P-TSEL-20K',  'cost' => 20850,  'sell' => 22000],
                ['name' => 'Pulsa Telkomsel 25.000',  'code' => 'P-TSEL-25K',  'cost' => 25650,  'sell' => 27000],
                ['name' => 'Pulsa Telkomsel 30.000',  'code' => 'P-TSEL-30K',  'cost' => 30650,  'sell' => 32000],
                ['name' => 'Pulsa Telkomsel 50.000',  'code' => 'P-TSEL-50K',  'cost' => 50650,  'sell' => 52000],
                ['name' => 'Pulsa Telkomsel 100.000', 'code' => 'P-TSEL-100K', 'cost' => 100650, 'sell' => 102000],
            ];
            $this->seedGroup($catIdTsel, $tselPulsa);
        }

        // ----------------------------------------------------
        // 2. SEEDER PULSA INDOSAT
        // ----------------------------------------------------
        $catIdIsat = $getCatId('pulsa-indosat');
        if ($catIdIsat) {
            $isatPulsa = [
                ['name' => 'Pulsa Indosat 5.000',   'code' => 'P-ISAT-5K',   'cost' => 6750,   'sell' => 7000],
                ['name' => 'Pulsa Indosat 10.000',  'code' => 'P-ISAT-10K',  'cost' => 11750,  'sell' => 12000],
                ['name' => 'Pulsa Indosat 12.000',  'code' => 'P-ISAT-12K',  'cost' => 13100,  'sell' => 14000],
                ['name' => 'Pulsa Indosat 15.000',  'code' => 'P-ISAT-15K',  'cost' => 16000,  'sell' => 17000],
                ['name' => 'Pulsa Indosat 20.000',  'code' => 'P-ISAT-20K',  'cost' => 21000,  'sell' => 22000],
                ['name' => 'Pulsa Indosat 25.000',  'code' => 'P-ISAT-25K',  'cost' => 26000,  'sell' => 27000],
                ['name' => 'Pulsa Indosat 30.000',  'code' => 'P-ISAT-30K',  'cost' => 31000,  'sell' => 32000],
                ['name' => 'Pulsa Indosat 35.000',  'code' => 'P-ISAT-35K',  'cost' => 36000,  'sell' => 37000], 
                ['name' => 'Pulsa Indosat 40.000',  'code' => 'P-ISAT-40K',  'cost' => 40500,  'sell' => 42000],
                ['name' => 'Pulsa Indosat 50.000',  'code' => 'P-ISAT-50K',  'cost' => 50500,  'sell' => 52000],
                ['name' => 'Pulsa Indosat 100.000', 'code' => 'P-ISAT-100K', 'cost' => 100500, 'sell' => 102000],
            ];
            $this->seedGroup($catIdIsat, $isatPulsa);
        }

        // ----------------------------------------------------
        // 3. SEEDER PULSA TRI (THREE)
        // ----------------------------------------------------
        $catIdTri = $getCatId('pulsa-tri');
        if ($catIdTri) {
            $triPulsa = [
                ['name' => 'Pulsa Tri 5.000',   'code' => 'P-3-5K',   'cost' => 5190,   'sell' => 7000],
                ['name' => 'Pulsa Tri 10.000',  'code' => 'P-3-10K',  'cost' => 10250,  'sell' => 12000],
                ['name' => 'Pulsa Tri 12.000',  'code' => 'P-3-12K',  'cost' => 13500,  'sell' => 14000],
                ['name' => 'Pulsa Tri 15.000',  'code' => 'P-3-15K',  'cost' => 14820,  'sell' => 17000],
                ['name' => 'Pulsa Tri 20.000',  'code' => 'P-3-20K',  'cost' => 19890,  'sell' => 22000],
                ['name' => 'Pulsa Tri 25.000',  'code' => 'P-3-25K',  'cost' => 24875,  'sell' => 27000],
                ['name' => 'Pulsa Tri 30.000',  'code' => 'P-3-30K',  'cost' => 29880,  'sell' => 32000],
                ['name' => 'Pulsa Tri 35.000',  'code' => 'P-3-35K',  'cost' => 34850,  'sell' => 37000],
                ['name' => 'Pulsa Tri 40.000',  'code' => 'P-3-40K',  'cost' => 39950,  'sell' => 42000],
                ['name' => 'Pulsa Tri 45.000',  'code' => 'P-3-45K',  'cost' => 45500,  'sell' => 47000],
                ['name' => 'Pulsa Tri 50.000',  'code' => 'P-3-50K',  'cost' => 49700,  'sell' => 52000],
                ['name' => 'Pulsa Tri 55.000',  'code' => 'P-3-55K',  'cost' => 55500,  'sell' => 57000],
                ['name' => 'Pulsa Tri 60.000',  'code' => 'P-3-60K',  'cost' => 60000,  'sell' => 62000],
                ['name' => 'Pulsa Tri 65.000',  'code' => 'P-3-65K',  'cost' => 65500,  'sell' => 67000],
                ['name' => 'Pulsa Tri 70.000',  'code' => 'P-3-70K',  'cost' => 70000,  'sell' => 72000],
                ['name' => 'Pulsa Tri 80.000',  'code' => 'P-3-80K',  'cost' => 79500,  'sell' => 82000],
                ['name' => 'Pulsa Tri 85.000',  'code' => 'P-3-85K',  'cost' => 85500,  'sell' => 87000],
                ['name' => 'Pulsa Tri 90.000',  'code' => 'P-3-90K',  'cost' => 89000,  'sell' => 92000],
                ['name' => 'Pulsa Tri 100.000', 'code' => 'P-3-100K', 'cost' => 99100,  'sell' => 102000],
                ['name' => 'Pulsa Tri 105.000', 'code' => 'P-3-105K', 'cost' => 105500, 'sell' => 109000],
                ['name' => 'Pulsa Tri 150.000', 'code' => 'P-3-150K', 'cost' => 150500, 'sell' => 152000],
                ['name' => 'Pulsa Tri 200.000', 'code' => 'P-3-200K', 'cost' => 200500, 'sell' => 202000],
                ['name' => 'Pulsa Tri 300.000', 'code' => 'P-3-300K', 'cost' => 300500, 'sell' => 302000],
                ['name' => 'Pulsa Tri 500.000', 'code' => 'P-3-500K', 'cost' => 500500, 'sell' => 502000],
            ];
            $this->seedGroup($catIdTri, $triPulsa);
        }

        // ----------------------------------------------------
        // 4. SEEDER PULSA SMARTFREN
        // ----------------------------------------------------
        $catIdSmart = $getCatId('pulsa-smartfren');
        if ($catIdSmart) {
            $smartPulsa = [
                ['name' => 'Pulsa Smartfren 2.000',   'code' => 'P-SMART-2K',   'cost' => 2100,   'sell' => 4000],   // 2.000 + 100
                ['name' => 'Pulsa Smartfren 3.000',   'code' => 'P-SMART-3K',   'cost' => 3100,   'sell' => 5000],   // 3.000 + 100
                ['name' => 'Pulsa Smartfren 4.000',   'code' => 'P-SMART-4K',   'cost' => 4100,   'sell' => 6000],   // 4.000 + 100
                ['name' => 'Pulsa Smartfren 5.000',   'code' => 'P-SMART-5K',   'cost' => 5100,   'sell' => 7000],   // 5.000 + 100
                ['name' => 'Pulsa Smartfren 6.000',   'code' => 'P-SMART-6K',   'cost' => 6100,   'sell' => 8000],   // 6.000 + 100
                ['name' => 'Pulsa Smartfren 7.000',   'code' => 'P-SMART-7K',   'cost' => 7100,   'sell' => 9000],   // 7.000 + 100
                ['name' => 'Pulsa Smartfren 8.000',   'code' => 'P-SMART-8K',   'cost' => 8100,   'sell' => 10000],  // 8.000 + 100
                ['name' => 'Pulsa Smartfren 9.000',   'code' => 'P-SMART-9K',   'cost' => 9100,   'sell' => 11000],  // 9.000 + 100
                ['name' => 'Pulsa Smartfren 10.000',  'code' => 'P-SMART-10K',  'cost' => 10100,  'sell' => 12000],  // 10.000 + 100
                ['name' => 'Pulsa Smartfren 11.000',  'code' => 'P-SMART-11K',  'cost' => 11100,  'sell' => 13000],  // 11.000 + 100
                ['name' => 'Pulsa Smartfren 12.000',  'code' => 'P-SMART-12K',  'cost' => 12100,  'sell' => 14000],  // 12.000 + 100
                ['name' => 'Pulsa Smartfren 15.000',  'code' => 'P-SMART-15K',  'cost' => 15150,  'sell' => 17000],  // 15.000 + 150
                ['name' => 'Pulsa Smartfren 20.000',  'code' => 'P-SMART-20K',  'cost' => 20150,  'sell' => 22000],  // 20.000 + 150
                ['name' => 'Pulsa Smartfren 25.000',  'code' => 'P-SMART-25K',  'cost' => 25150,  'sell' => 27000],  // 25.000 + 150
                ['name' => 'Pulsa Smartfren 30.000',  'code' => 'P-SMART-30K',  'cost' => 30150,  'sell' => 32000],  // 30.000 + 150
                ['name' => 'Pulsa Smartfren 35.000',  'code' => 'P-SMART-35K',  'cost' => 35150,  'sell' => 37000],  // 35.000 + 150
                ['name' => 'Pulsa Smartfren 40.000',  'code' => 'P-SMART-40K',  'cost' => 40200,  'sell' => 42000],  // 40.000 + 200
                ['name' => 'Pulsa Smartfren 45.000',  'code' => 'P-SMART-45K',  'cost' => 45200,  'sell' => 47000],  // 45.000 + 200
                ['name' => 'Pulsa Smartfren 50.000',  'code' => 'P-SMART-50K',  'cost' => 50200,  'sell' => 52000],  // 50.000 + 200
                ['name' => 'Pulsa Smartfren 55.000',  'code' => 'P-SMART-55K',  'cost' => 55200,  'sell' => 57000],  // 55.000 + 200
                ['name' => 'Pulsa Smartfren 60.000',  'code' => 'P-SMART-60K',  'cost' => 60200,  'sell' => 62000],  // 60.000 + 200
                ['name' => 'Pulsa Smartfren 65.000',  'code' => 'P-SMART-65K',  'cost' => 65200,  'sell' => 67000],  // 65.000 + 200
                ['name' => 'Pulsa Smartfren 70.000',  'code' => 'P-SMART-70K',  'cost' => 70200,  'sell' => 72000],  // 70.000 + 200
                ['name' => 'Pulsa Smartfren 75.000',  'code' => 'P-SMART-75K',  'cost' => 75200,  'sell' => 77000],  // 75.000 + 200
                ['name' => 'Pulsa Smartfren 80.000',  'code' => 'P-SMART-80K',  'cost' => 80200,  'sell' => 82000],  // 80.000 + 200
                ['name' => 'Pulsa Smartfren 85.000',  'code' => 'P-SMART-85K',  'cost' => 85200,  'sell' => 87000],  // 85.000 + 200
                ['name' => 'Pulsa Smartfren 90.000',  'code' => 'P-SMART-90K',  'cost' => 90200,  'sell' => 92000],  // 90.000 + 200
                ['name' => 'Pulsa Smartfren 95.000',  'code' => 'P-SMART-95K',  'cost' => 95200,  'sell' => 97000],  // 95.000 + 200
                ['name' => 'Pulsa Smartfren 100.000', 'code' => 'P-SMART-100K', 'cost' => 100300, 'sell' => 102000], // 100.000 + 300
                ['name' => 'Pulsa Smartfren 125.000', 'code' => 'P-SMART-125K', 'cost' => 125600, 'sell' => 127000], // 125.000 + 600
                ['name' => 'Pulsa Smartfren 150.000', 'code' => 'P-SMART-150K', 'cost' => 150300, 'sell' => 152000], // 150.000 + 300
                ['name' => 'Pulsa Smartfren 200.000', 'code' => 'P-SMART-200K', 'cost' => 200300, 'sell' => 202000], // 200.000 + 300
            ];
            $this->seedGroup($catIdSmart, $smartPulsa);
        }

        // ----------------------------------------------------
        // 5. SEEDER PULSA XL
        // ----------------------------------------------------
        $catIdXL = $getCatId('pulsa-xl');
        if ($catIdXL) {
            $xlPulsa = [
                ['name' => 'Pulsa XL 5.000',   'code' => 'P-XL-5K',   'cost' => 5850,   'sell' => 7000],
                ['name' => 'Pulsa XL 10.000',  'code' => 'P-XL-10K',  'cost' => 10850,  'sell' => 12000], 
                ['name' => 'Pulsa XL 15.000',  'code' => 'P-XL-15K',  'cost' => 15500,  'sell' => 17000],
                ['name' => 'Pulsa XL 20.000',  'code' => 'P-XL-20K',  'cost' => 20000,  'sell' => 22000],
                ['name' => 'Pulsa XL 25.000',  'code' => 'P-XL-25K',  'cost' => 25000,  'sell' => 27000],
                ['name' => 'Pulsa XL 30.000',  'code' => 'P-XL-30K',  'cost' => 30000,  'sell' => 32000],
                ['name' => 'Pulsa XL 50.000',  'code' => 'P-XL-50K',  'cost' => 50000,  'sell' => 52000],
                ['name' => 'Pulsa XL 100.000', 'code' => 'P-XL-100K', 'cost' => 100000, 'sell' => 102000],
            ];
            $this->seedGroup($catIdXL, $xlPulsa);
        }

        // ----------------------------------------------------
        // 6. SEEDER PULSA AXIS
        // ----------------------------------------------------
        $catIdAxis = $getCatId('pulsa-axis');
if ($catIdAxis) {
    $axisPulsa = [
        ['name' => 'Pulsa AXIS 5.000',   'code' => 'P-AXIS-5K',   'cost' => 5850,   'sell' => 7000], 
        ['name' => 'Pulsa AXIS 10.000',  'code' => 'P-AXIS-10K',  'cost' => 10850,  'sell' => 12000],
        ['name' => 'Pulsa AXIS 15.000',  'code' => 'P-AXIS-15K',  'cost' => 15500,  'sell' => 17000],
        ['name' => 'Pulsa AXIS 20.000',  'code' => 'P-AXIS-20K',  'cost' => 20000,  'sell' => 22000],
        ['name' => 'Pulsa AXIS 25.000',  'code' => 'P-AXIS-25K',  'cost' => 25000,  'sell' => 27000],
        ['name' => 'Pulsa AXIS 30.000',  'code' => 'P-AXIS-30K',  'cost' => 30000,  'sell' => 32000],
        ['name' => 'Pulsa AXIS 50.000',  'code' => 'P-AXIS-50K',  'cost' => 50000,  'sell' => 52000],
        ['name' => 'Pulsa AXIS 100.000', 'code' => 'P-AXIS-100K', 'cost' => 100000, 'sell' => 102000],
    ];
    $this->seedGroup($catIdAxis, $axisPulsa);
}
    }

    private function seedGroup($catId, array $pulsas)
    {
        foreach ($pulsas as $p) {
            // 1. Buat Master Produk (Global) tipe Digital
            $product = Product::create([
                'category_id'   => $catId,
                'name'          => $p['name'],
                'code'          => $p['code'],
                'type'          => 'digital',
                'cost_price'    => $p['cost'],
                'selling_price' => $p['sell'],
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