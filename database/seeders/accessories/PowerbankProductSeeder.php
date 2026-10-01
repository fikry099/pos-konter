<?php

namespace Database\Seeders\Accessories;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreProductStock;

class PowerbankProductSeeder extends Seeder
{
    public function run(): void
    {
        // Targetkan Khusus Store / Cabang ID 2
        $targetStoreId = 2;

        // 1. Ambil atau Buat Kategori Induk Aksesoris HP
        $parentCategory = Category::firstOrCreate(
            ['slug' => 'aksesoris-hp'],
            ['name' => 'Aksesoris HP']
        );

        // 2. Ambil atau Buat Sub-Kategori Powerbank
        $category = Category::firstOrCreate(
            ['slug' => 'powerbank'],
            [
                'name'      => 'Powerbank',
                'parent_id' => $parentCategory->id
            ]
        );

        // 3. Rekapitulasi Data Barang dari Foto Catatan
        $items = [
            ['name' => 'Box PB Boldwave', 'code' => 'BW-PB11', 'brand' => 'boldwave', 'cost' => 74000, 'price' => 110000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box PB Oraimo', 'code' => 'OPB-1100D', 'brand' => 'oraimo', 'cost' => 100000, 'price' => 150000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box PB TECNIX', 'code' => 'PWB-128', 'brand' => 'tecnix', 'cost' => 95000, 'price' => 135000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box PB Veger', 'code' => 'PB-41', 'brand' => 'veger', 'cost' => 65000, 'price' => 95000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box PB Nicol', 'code' => 'NP-01', 'brand' => 'nicol', 'cost' => 65000, 'price' => 95000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box PB Vizz', 'code' => 'PD18W', 'brand' => 'vizz', 'cost' => 95000, 'price' => 165000, 'stock' => 1, 'min_stock' => 2],
        ];

        // 4. Masukkan Produk ke Database Master & Stok Spesifik Store ID 2
        foreach ($items as $item) {
            $product = Product::updateOrCreate(
                ['code' => $item['code']],
                [
                    'category_id'   => $category->id,
                    'name'          => $item['name'],
                    'brand'         => $item['brand'],
                    'cost_price'    => $item['cost'],
                    'selling_price' => $item['price'],
                    'stock'         => 0, // Master stock 0 agar tersentralisasi di store_id tertentu
                    'min_stock'     => $item['min_stock'],
                    'type'          => 'physical',
                    'is_active'     => true,
                ]
            );

            // KHUSUS STORE ID 2
            StoreProductStock::updateOrCreate(
                [
                    'store_id'   => $targetStoreId,
                    'product_id' => $product->id,
                ],
                [
                    'cost_price'    => $item['cost'],
                    'selling_price' => $item['price'],
                    'stock'         => $item['stock'],
                    'min_stock'     => $item['min_stock'],
                ]
            );
        }
    }
}