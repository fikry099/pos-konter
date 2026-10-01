<?php

namespace Database\Seeders\Accessories;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreProductStock;

class AdapterHeadProductSeeder extends Seeder
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

        // 2. Ambil atau Buat Sub-Kategori Adaptor / Kepala Charger
        $category = Category::firstOrCreate(
            ['slug' => 'adaptor-kepala'],
            [
                'name'      => 'Adaptor / Kepala Charger',
                'parent_id' => $parentCategory->id
            ]
        );

        // 3. Rekapitulasi Data Barang
        $items = [
            ['name' => 'TOP Adaptor Roket 2.1A', 'code' => 'RTC02', 'brand' => 'roket', 'cost' => 13000, 'price' => 30000, 'stock' => 11, 'min_stock' => 20],
            ['name' => 'TOP Adaptor Roket 3.A', 'code' => 'RTC08', 'brand' => 'roket', 'cost' => 15000, 'price' => 55000, 'stock' => 8, 'min_stock' => 10],
            ['name' => 'TOP Adaptor Roket 2 USB', 'code' => 'RTC-05', 'brand' => 'roket', 'cost' => 20000, 'price' => 45000, 'stock' => 7, 'min_stock' => 10],
            ['name' => 'TOP Adaptor Roket USB + C', 'code' => 'RTC-09', 'brand' => 'roket', 'cost' => 45000, 'price' => 75000, 'stock' => 8, 'min_stock' => 10],
            ['name' => 'TOP Adaptor Olike C', 'code' => 'C308', 'brand' => 'olike', 'cost' => 45000, 'price' => 70000, 'stock' => 2, 'min_stock' => 10],
            ['name' => 'BOX Adaptor Wellcomm 25W', 'code' => 'AW-12', 'brand' => 'wellcomm', 'cost' => 50000, 'price' => 75000, 'stock' => 0, 'min_stock' => 10],
            ['name' => 'BOX Adaptor Vivan C', 'code' => 'PowerLine 30S', 'brand' => 'vivan', 'cost' => 86000, 'price' => 125000, 'stock' => 2, 'min_stock' => 3],
            ['name' => 'BOX Adaptor Robot 3A', 'code' => 'RT-F1', 'brand' => 'robot', 'cost' => 50000, 'price' => 80000, 'stock' => 1, 'min_stock' => 3],
            ['name' => 'BOX Adaptor Robot 2 USB', 'code' => 'RT-F3', 'brand' => 'robot', 'cost' => 58000, 'price' => 85000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'BOX Adaptor Oraimo C', 'code' => 'OCW-5201E', 'brand' => 'oraimo', 'cost' => 55000, 'price' => 95000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'BOX Adaptor Oraimo C AO-20', 'code' => 'AO-20', 'brand' => 'oraimo', 'cost' => 70000, 'price' => 110000, 'stock' => 6, 'min_stock' => 6],
            ['name' => 'BOX Adaptor Olike USB + C', 'code' => 'C-400', 'brand' => 'olike', 'cost' => 43000, 'price' => 70000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'BOX Adaptor Olike 33W', 'code' => 'C306', 'brand' => 'olike', 'cost' => 79000, 'price' => 120000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'BOX Adaptor Samsung Ori', 'code' => 'AS-01', 'brand' => 'samsung', 'cost' => 80000, 'price' => 135000, 'stock' => 7, 'min_stock' => 10],
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
                    'stock'         => 0, // Master stock dibuat 0 agar tidak tampil di store_id lain
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