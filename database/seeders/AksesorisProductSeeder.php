<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProductStock;
use Illuminate\Database\Seeder;

class AksesorisProductSeeder extends Seeder
{
    public function run(): void
    {
        $getCatId = function ($slug) {
            $cat = Category::where('slug', $slug)->first();
            return $cat ? $cat->id : Category::whereIn('slug', ['aksesoris', 'aksesoris-hp'])->first()?->id;
        };

        // Menggunakan slug utama agar presisi dengan tombol di sub-providers view
        $catPower     = $getCatId('power') ?? $getCatId('aksesoris');
        $catAudio     = $getCatId('audio') ?? $getCatId('aksesoris');
        $catProteksi  = $getCatId('proteksi') ?? $getCatId('aksesoris');

        $accessories = [
            // Kategori POWER (Charger, Kabel, PB)
            [
                'category_id' => $catPower, 
                'name'        => 'Kabel Data Type-C Fast Charging Power 1M', 
                'code'        => 'ACC-TYPEC-01',   
                'cost'        => 8000,  
                'sell'        => 15000, 
                'stock'       => 10, 
                'min_stock'   => 3
            ],
            [
                'category_id' => $catPower, 
                'name'        => 'Kabel Data Lightning iPhone Power 1M',     
                'code'        => 'ACC-IPHONE-01',  
                'cost'        => 12000, 
                'sell'        => 25000, 
                'stock'       => 8,  
                'min_stock'   => 3
            ],
            [
                'category_id' => $catPower, 
                'name'        => 'Charger Batok Fast Charging Power 18W',    
                'code'        => 'ACC-CHARGER-18W',
                'cost'        => 22000, 
                'sell'        => 40000, 
                'stock'       => 6,  
                'min_stock'   => 2
            ],

            // Kategori AUDIO (Headset, TWS)
            [
                'category_id' => $catAudio, 
                'name'        => 'Headset Audio Bass Extra Jack 3.5mm',
                'code'        => 'ACC-HEADSET-01', 
                'cost'        => 15000, 
                'sell'        => 30000, 
                'stock'       => 5,  
                'min_stock'   => 2
            ],

            // Kategori PROTEKSI (Casing, TG)
            [
                'category_id' => $catProteksi, 
                'name'        => 'Softcase Casing Proteksi Bening iPhone 11',
                'code'        => 'ACC-CASE-IP11',  
                'cost'        => 5000,  
                'sell'        => 20000, 
                'stock'       => 1,  
                'min_stock'   => 1
            ],
        ];

        $this->seedGroup($accessories);
    }

    private function seedGroup(array $items)
    {
        foreach ($items as $item) {
            if (!$item['category_id']) continue;

            $product = Product::updateOrCreate(
                ['code' => $item['code']],
                [
                    'category_id'   => $item['category_id'],
                    'name'          => $item['name'],
                    'type'          => 'physical',
                    'cost_price'    => $item['cost'],
                    'selling_price' => $item['sell'],
                    'stock'         => 1,
                    'min_stock'     => 1,
                    'is_active'     => true,
                ]
            );

            StoreProductStock::updateOrCreate(
                ['store_id' => 1, 'product_id' => $product->id],
                [
                    'stock'     => $item['store1_stock'] ?? $item['stock'] ?? 2,
                    'min_stock' => $item['store1_min']   ?? $item['min_stock'] ?? 3,
                ]
            );

            StoreProductStock::updateOrCreate(
                ['store_id' => 2, 'product_id' => $product->id],
                [
                    'stock'     => $item['store2_stock'] ?? $item['stock'] ?? 1,
                    'min_stock' => $item['store2_min']   ?? $item['min_stock'] ?? 3,
                ]
            );
        }
    }
}