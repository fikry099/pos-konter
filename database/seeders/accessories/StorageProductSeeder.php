<?php

namespace Database\Seeders\Accessories;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreProductStock;

class StorageProductSeeder extends Seeder
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

        // 2. Ambil atau Buat Sub-Kategori Memori / Flashdisk / MMC
        $category = Category::firstOrCreate(
            ['slug' => 'mmc-flashdisk'],
            [
                'name'      => 'MMC & Flashdisk',
                'parent_id' => $parentCategory->id
            ]
        );

        // 3. Rekapitulasi Data Barang Berdasarkan Gambar Catatan
        $items = [
            // GAMBAR 1: DATA BOX MMC & FLASHDISK
            ['name' => 'Box MMC 4GB Olike', 'code' => 'TF-04', 'brand' => 'olike', 'cost' => 65000, 'price' => 95000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box MMC 8GB Olike', 'code' => 'TF-08', 'brand' => 'olike', 'cost' => 90000, 'price' => 120000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box MMC 32GB Olike', 'code' => 'TF-32', 'brand' => 'olike', 'cost' => 165000, 'price' => 200000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box MMC 64GB Olike', 'code' => 'TF-64', 'brand' => 'olike', 'cost' => 214000, 'price' => 300000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box MMC V-GEN 32GB', 'code' => 'M-32', 'brand' => 'v-gen', 'cost' => 65000, 'price' => 90000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box MMC TOSHIBA 32GB', 'code' => 'M-31', 'brand' => 'toshiba', 'cost' => 22000, 'price' => 70000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box MMC TOSHIBA 64GB', 'code' => 'M-64', 'brand' => 'toshiba', 'cost' => 23000, 'price' => 80000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Flash Disk 32GB Olike', 'code' => 'OFP-232', 'brand' => 'olike', 'cost' => 113000, 'price' => 145000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Flash Disk 64GB Olike', 'code' => 'OFP-264', 'brand' => 'olike', 'cost' => 160000, 'price' => 195000, 'stock' => 1, 'min_stock' => 2],

            // GAMBAR 2: MEMORY CARD ROBOT, VIVAN & FLASHDISK ROBOT
            // Memory Card Robot
            ['name' => 'MMC Robot 4GB', 'code' => 'R4', 'brand' => 'robot', 'cost' => 72000, 'price' => 95000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Robot 8GB', 'code' => 'R8', 'brand' => 'robot', 'cost' => 90000, 'price' => 115000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Robot 16GB', 'code' => 'R16', 'brand' => 'robot', 'cost' => 95000, 'price' => 125000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Robot 32GB', 'code' => 'R32', 'brand' => 'robot', 'cost' => 168000, 'price' => 199000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Robot 64GB', 'code' => 'R64', 'brand' => 'robot', 'cost' => 249000, 'price' => 285000, 'stock' => 0, 'min_stock' => 2],

            // Memory Card Vivan
            ['name' => 'MMC Vivan 16GB', 'code' => 'V16', 'brand' => 'vivan', 'cost' => 102000, 'price' => 130000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Vivan 32GB', 'code' => 'V32', 'brand' => 'vivan', 'cost' => 179000, 'price' => 210000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'MMC Vivan 64GB', 'code' => 'V64', 'brand' => 'vivan', 'cost' => 299000, 'price' => 335000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Vivan 128GB', 'code' => 'V128', 'brand' => 'vivan', 'cost' => 480000, 'price' => 520000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'MMC Vivan 256GB', 'code' => 'V256', 'brand' => 'vivan', 'cost' => 444000, 'price' => 485000, 'stock' => 0, 'min_stock' => 2],

            // Flashdisk Robot
            ['name' => 'Flashdisk Robot 8GB', 'code' => 'FD-R8', 'brand' => 'robot', 'cost' => 72000, 'price' => 99000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'Flashdisk Robot 16GB', 'code' => 'FD-R16', 'brand' => 'robot', 'cost' => 85000, 'price' => 115000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'Flashdisk Robot 32GB', 'code' => 'FD-R32', 'brand' => 'robot', 'cost' => 102000, 'price' => 130000, 'stock' => 0, 'min_stock' => 2],
            ['name' => 'Flashdisk Robot 64GB', 'code' => 'FD-R64', 'brand' => 'robot', 'cost' => 151000, 'price' => 180000, 'stock' => 0, 'min_stock' => 2],
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