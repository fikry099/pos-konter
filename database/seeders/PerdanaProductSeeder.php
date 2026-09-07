<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PerdanaProductSeeder extends Seeder
{
    public function run(): void
    {
        // Helper function untuk mengambil/fallback category ID
        $getCatId = function ($slug) {
            $cat = Category::where('slug', $slug)->first();
            return $cat ? $cat->id : Category::where('slug', 'kartu-perdana')->first()?->id;
        };

        // 1. KARTU PERDANA THREE (TRI)
        $catIdTri = $getCatId('perdana-tri');
        if ($catIdTri) {
            $triPerdana = [
                ['name' => 'Perdana Tri 3GB', 'code' => 'SP-3-3GB', 'cost' => 15000, 'sell' => 35000, 'stock' => 5, 'min_stock' => 5],
            ];
            $this->seedGroup($catIdTri, $triPerdana);
        }

        // 2. KARTU PERDANA SMARTFREN
        $catIdSmart = $getCatId('perdana-smartfren');
        if ($catIdSmart) {
            $smartPerdana = [
                ['name' => 'Perdana Smartfren 3GB',  'code' => 'SP-SMART-3GB',  'cost' => 11000, 'sell' => 14000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Perdana Smartfren 10GB', 'code' => 'SP-SMART-10GB', 'cost' => 22000, 'sell' => 26000, 'stock' => 3, 'min_stock' => 3],
            ];
            $this->seedGroup($catIdSmart, $smartPerdana);
        }

        // 3. KARTU PERDANA TELKOMSEL
        $catIdTsel = $getCatId('perdana-telkomsel');
        if ($catIdTsel) {
            $tselPerdana = [
                ['name' => 'Perdana Telkomsel 3GB',     'code' => 'SP-TSEL-3GB', 'cost' => 14000, 'sell' => 17000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Perdana Telkomsel By.U 3GB', 'code' => 'SP-BYU-3GB',  'cost' => 13000, 'sell' => 16000, 'stock' => 5, 'min_stock' => 5],
            ];
            $this->seedGroup($catIdTsel, $tselPerdana);
        }

        // 4. KARTU PERDANA AXIS
        $catIdAxis = $getCatId('perdana-axis');
        if ($catIdAxis) {
            $axisPerdana = [
                ['name' => 'Perdana Axis 3GB', 'code' => 'SP-AXIS-3GB', 'cost' => 10000, 'sell' => 13000, 'stock' => 5, 'min_stock' => 5],
            ];
            $this->seedGroup($catIdAxis, $axisPerdana);
        }

        // 5. KARTU PERDANA XL
        $catIdXL = $getCatId('perdana-xl');
        if ($catIdXL) {
            $xlPerdana = [
                ['name' => 'Perdana XL 3GB',  'code' => 'SP-XL-3GB',  'cost' => 13000, 'sell' => 16000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Perdana XL 10GB', 'code' => 'SP-XL-10GB', 'cost' => 24000, 'sell' => 28000, 'stock' => 3, 'min_stock' => 3],
            ];
            $this->seedGroup($catIdXL, $xlPerdana);
        }

        // 6. KARTU PERDANA INDOSAT
        $catIdIsat = $getCatId('perdana-indosat');
        if ($catIdIsat) {
            $isatPerdana = [
                ['name' => 'Perdana Indosat 3GB',  'code' => 'SP-ISAT-3GB',  'cost' => 12000, 'sell' => 15000, 'stock' => 5, 'min_stock' => 5],
                ['name' => 'Perdana Indosat 10GB', 'code' => 'SP-ISAT-10GB', 'cost' => 23000, 'sell' => 27000, 'stock' => 3, 'min_stock' => 3],
            ];
            $this->seedGroup($catIdIsat, $isatPerdana);
        }
    }

    private function seedGroup($catId, array $perdanas)
    {
        foreach ($perdanas as $sp) {
            Product::create([
                'category_id'   => $catId,
                'name'          => $sp['name'],
                'code'          => $sp['code'],
                'type'          => 'physical',
                'cost_price'    => $sp['cost'],
                'selling_price' => $sp['sell'],
                'stock'         => $sp['stock'],
                'min_stock'     => $sp['min_stock'],
                'is_active'     => true,
            ]);
        }
    }
}