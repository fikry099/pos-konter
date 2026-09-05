<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AksesorisProductSeeder extends Seeder
{
    public function run(): void
    {
        $catAksesorisRoot = Category::where('slug', 'aksesoris')->first();
        $fallbackAksesoris = $catAksesorisRoot ? $catAksesorisRoot->id : null;

        $catKabelData = Category::where('slug', 'kabel-data')->first()?->id ?? $fallbackAksesoris;
        $catHeadset   = Category::where('slug', 'headset')->first()?->id ?? $fallbackAksesoris;
        $catCharger   = Category::where('slug', 'charger')->first()?->id ?? $fallbackAksesoris;
        $catCasing    = Category::where('slug', 'casing')->first()?->id ?? $fallbackAksesoris;

        $accessories = [
            ['category_id' => $catKabelData, 'name' => 'Kabel Data Type-C Fast Charging 1M', 'code' => 'ACC-TYPEC-01',   'cost' => 8000,  'sell' => 15000, 'stock' => 10],
            ['category_id' => $catKabelData, 'name' => 'Kabel Data Lightning iPhone 1M',     'code' => 'ACC-IPHONE-01',  'cost' => 12000, 'sell' => 25000, 'stock' => 8],
            ['category_id' => $catHeadset,   'name' => 'Headset Bass Extra Audio Jack 3.5mm','code' => 'ACC-HEADSET-01', 'cost' => 15000, 'sell' => 30000, 'stock' => 5],
            ['category_id' => $catCharger,   'name' => 'Charger Batok Fast Charging 18W',    'code' => 'ACC-CHARGER-18W','cost' => 22000, 'sell' => 40000, 'stock' => 6],
            ['category_id' => $catCasing,    'name' => 'Softcase Bening Universal iPhone 11','code' => 'ACC-CASE-IP11',  'cost' => 5000,  'sell' => 20000, 'stock' => 1],
        ];

        foreach ($accessories as $a) {
            if ($a['category_id']) {
                Product::create([
                    'category_id'   => $a['category_id'],
                    'name'          => $a['name'],
                    'code'          => $a['code'],
                    'type'          => 'physical',
                    'cost_price'    => $a['cost'],
                    'selling_price' => $a['sell'],
                    'stock'         => $a['stock'],
                    'min_stock'     => 3,
                    'is_active'     => true,
                ]);
            }
        }
    }
}