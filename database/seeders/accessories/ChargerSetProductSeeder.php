<?php

namespace Database\Seeders\Accessories;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreProductStock;

class ChargerSetProductSeeder extends Seeder
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

        // 2. Ambil atau Buat Sub-Kategori Adaptor 1Set & Car Charger
        $category = Category::firstOrCreate(
            ['slug' => 'adaptor-set-car'],
            [
                'name'      => 'Adaptor 1Set & Car Charger',
                'parent_id' => $parentCategory->id
            ]
        );

        // 3. Rekapitulasi Data Barang (Sesuai List Baris Stabilo Kuning & Catatan Tambahan)
        $items = [
            // --- GAMBAR 1 (HALAMAN 1) ---
            ['name' => 'Box Type C 1Set Xiaomi', 'code' => '1Set-C-X', 'brand' => 'xiaomi', 'cost' => 50000, 'price' => 80000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C 1Set Well', 'code' => '1Set-MIC-W', 'brand' => 'wellcomm', 'cost' => 75000, 'price' => 95000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Travel', 'code' => '1Set-C', 'brand' => 'other', 'cost' => 25000, 'price' => 45000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Iphone Travel', 'code' => '1Set-IP-T', 'brand' => 'other', 'cost' => 25000, 'price' => 45000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Iphone PINK', 'code' => '1Set-IP', 'brand' => 'other', 'cost' => 90000, 'price' => 140000, 'stock' => 2, 'min_stock' => 2],

            // --- GAMBAR 2 (HALAMAN 2) ---
            ['name' => 'Box Micro Hikaru', 'code' => '1Set-MIC-H', 'brand' => 'hikaru', 'cost' => 29000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Travel', 'code' => '1Set-MIC-T', 'brand' => 'other', 'cost' => 20000, 'price' => 40000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Hikaru', 'code' => '1Set-C-H', 'brand' => 'hikaru', 'cost' => 31000, 'price' => 55000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Micro Wellcome', 'code' => '1Set-MIC-W2', 'brand' => 'wellcomm', 'cost' => 55000, 'price' => 70000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C Wellcomm', 'code' => '1Set-CTC-WELL', 'brand' => 'wellcomm', 'cost' => 30000, 'price' => 55000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Type C Wellcome', 'code' => '1Set-C-WELL', 'brand' => 'wellcomm', 'cost' => 17000, 'price' => 55000, 'stock' => 4, 'min_stock' => 5],
            ['name' => 'Box Micro Wellcomm', 'code' => '1Set-MIC-WELL', 'brand' => 'wellcomm', 'cost' => 30000, 'price' => 50000, 'stock' => 2, 'min_stock' => 2],

            // --- GAMBAR 3 (HALAMAN 3) ---
            ['name' => 'Box 1Set Micro DAP', 'code' => 'DAQ3', 'brand' => 'dap', 'cost' => 47000, 'price' => 75000, 'stock' => 1, 'min_stock' => 2],

            // --- GAMBAR 4 (HALAMAN 4) ---
            ['name' => 'Box 1Set Micro Oraimo', 'code' => 'OCW-5133-E1053', 'brand' => 'oraimo', 'cost' => 46000, 'price' => 80000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box C to IP Oraimo 1Set', 'code' => '1SET-OCW-E106ST', 'brand' => 'oraimo', 'cost' => 61000, 'price' => 105000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box C to IP Oraimo 1Set ECL', 'code' => 'OCW-5202-ECL', 'brand' => 'oraimo', 'cost' => 60000, 'price' => 95000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box Iphone Oraimo 1Set', 'code' => 'OCW-1111E-L53', 'brand' => 'oraimo', 'cost' => 50000, 'price' => 90000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box C to IP Oraimo 1Set 5203', 'code' => 'OCW-5203-ECL', 'brand' => 'oraimo', 'cost' => 39000, 'price' => 65000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Oraimo 1Set', 'code' => 'OCW-5184-EM53', 'brand' => 'oraimo', 'cost' => 63000, 'price' => 95000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Log-on 1Set', 'code' => 'LO-C33', 'brand' => 'log-on', 'cost' => 40000, 'price' => 60000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box Type C Log-on 1Set', 'code' => 'LO-C33-C', 'brand' => 'log-on', 'cost' => 40000, 'price' => 65000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box Iphone Log-on 1Set', 'code' => 'LO-C33M', 'brand' => 'log-on', 'cost' => 33000, 'price' => 60000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box Micro RAPA 1Set', 'code' => 'CH4145', 'brand' => 'rapa', 'cost' => 39000, 'price' => 65000, 'stock' => 5, 'min_stock' => 5],

            // --- GAMBAR 5 (HALAMAN 5) ---
            ['name' => 'Box C to C Olike 1Set', 'code' => 'C401-OLIKE', 'brand' => 'olike', 'cost' => 52000, 'price' => 90000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Olike 1Set C401', 'code' => 'C401-C', 'brand' => 'olike', 'cost' => 80000, 'price' => 110000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Olike 1Set C301', 'code' => 'C301-C', 'brand' => 'olike', 'cost' => 40000, 'price' => 70000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to IP Olike 1Set', 'code' => 'C308-CL', 'brand' => 'olike', 'cost' => 52000, 'price' => 85000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Tecno 1Set', 'code' => 'CHR-094-M', 'brand' => 'tecno', 'cost' => 35000, 'price' => 65000, 'stock' => 2, 'min_stock' => 2],

            // --- GAMBAR BARU (DATA CAR CHARGER) ---
            ['name' => 'Box Car Charger Oraimo Set', 'code' => 'OCC-1152D-SET', 'brand' => 'oraimo', 'cost' => 65000, 'price' => 95000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Car Charger Oraimo', 'code' => 'OCC-1152D', 'brand' => 'oraimo', 'cost' => 38000, 'price' => 60000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Car Charger Wellcomm', 'code' => 'CW-00', 'brand' => 'wellcomm', 'cost' => 70000, 'price' => 110000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Car Charger Olike PD 30W', 'code' => 'PD-30W', 'brand' => 'olike', 'cost' => 36000, 'price' => 85000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'ETA Car Charger Olike 3A', 'code' => 'R13', 'brand' => 'olike', 'cost' => 27000, 'price' => 50000, 'stock' => 7, 'min_stock' => 2],
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