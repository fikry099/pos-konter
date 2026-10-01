<?php

namespace Database\Seeders\Accessories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreProductStock;
use Illuminate\Database\Seeder;

class HeadsetEarphoneProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil/Buat Parent Category: Aksesoris HP
        $parentCategory = Category::firstOrCreate(
            ['slug' => 'aksesoris-hp'],
            [
                'name' => 'Aksesoris HP',
                'parent_id' => null,
            ]
        );

        // 2. Ambil/Buat Sub-Category: Headset / Earphone
        $headsetCategory = Category::firstOrCreate(
            ['slug' => 'headset-earphone'],
            [
                'name' => 'Headset / Earphone',
                'parent_id' => $parentCategory->id,
            ]
        );

        // 3. Ambil Semua Cabang Store yang Ada
        $stores = Store::all();
        if ($stores->isEmpty()) {
            $stores = collect([Store::firstOrCreate(['name' => 'Toko Utama', 'is_active' => true])]);
        }

        // 4. List Data Produk Headset / Earphone (Gambar 1 & Gambar 2)
        $productsData = [
            // --- GAMBAR 1 (Wireless / Bluetooth / TWS) ---
            ['name' => 'Box Toomee QA02', 'code' => 'QA02', 'stock' => 1, 'cost_price' => 13000, 'selling_price' => 25000],
            ['name' => 'Box Onlite NS 12', 'code' => 'NS 12', 'stock' => 1, 'cost_price' => 13000, 'selling_price' => 25000],
            ['name' => 'Box Onlite OS 12', 'code' => 'OS 12', 'stock' => 1, 'cost_price' => 13000, 'selling_price' => 20000],
            ['name' => 'Box Onlite AS 12', 'code' => 'AS 12', 'stock' => 2, 'cost_price' => 13000, 'selling_price' => 25000],
            ['name' => 'Box Onlite HP101', 'code' => 'HP101', 'stock' => 1, 'cost_price' => 13000, 'selling_price' => 25000],
            ['name' => 'Box DAP DHF 3', 'code' => 'DHF 3', 'stock' => 2, 'cost_price' => 25000, 'selling_price' => 50000],
            ['name' => 'Box Onesam OS X03', 'code' => 'OS X03', 'stock' => 2, 'cost_price' => 15000, 'selling_price' => 40000],
            ['name' => 'Box DAP DHF 13', 'code' => 'DHF 13', 'stock' => 1, 'cost_price' => 21000, 'selling_price' => 40000],
            ['name' => 'Box Wellcomm HF-Stereo-BS01', 'code' => 'HF-Stereo-BS01', 'stock' => 1, 'cost_price' => 29000, 'selling_price' => 50000],
            ['name' => 'Box Erp Tecnix EP-T78', 'code' => 'EP-T78', 'stock' => 1, 'cost_price' => 80000, 'selling_price' => 135000],
            ['name' => 'Box Erp Robot T70', 'code' => 'T70', 'stock' => 1, 'cost_price' => 121000, 'selling_price' => 175000],
            ['name' => 'Box Erp Robot Nova T3', 'code' => 'Nova T3', 'stock' => 1, 'cost_price' => 110000, 'selling_price' => 165000],
            ['name' => 'Box Erp Oraimo OTW-330S', 'code' => 'OTW-330S', 'stock' => 1, 'cost_price' => 99000, 'selling_price' => 150000],
            ['name' => 'Box Erp Oraimo OTW-323', 'code' => 'OTW-323', 'stock' => 1, 'cost_price' => 112000, 'selling_price' => 165000],
            ['name' => 'Box Olike Erp OH-T107', 'code' => 'OH-T107', 'stock' => 1, 'cost_price' => 199000, 'selling_price' => 250000],
            ['name' => 'Box Erp J M19', 'code' => 'M19', 'stock' => 2, 'cost_price' => 42000, 'selling_price' => 95000],
            ['name' => 'Box Erp Pro 3 HTS', 'code' => 'Pro 3 HTS', 'stock' => 6, 'cost_price' => 35000, 'selling_price' => 85000],
            ['name' => 'Box Erp Ultrapods Wireless 5.3', 'code' => 'Wireless 5.3', 'stock' => 2, 'cost_price' => 55000, 'selling_price' => 90000],

            // --- GAMBAR 2 (Wired / Kabel & Type-C) ---
            ['name' => 'Meemax Box EJ-D201', 'code' => 'EJ-D201', 'stock' => 1, 'cost_price' => 15000, 'selling_price' => 30000],
            ['name' => 'Box Headset Random HR-0', 'code' => 'HR-0', 'stock' => 4, 'cost_price' => 10000, 'selling_price' => 20000],
            ['name' => 'Box Onlite HF-10', 'code' => 'HF-10', 'stock' => 2, 'cost_price' => 18000, 'selling_price' => 35000],
            ['name' => 'Box Headset Random HR-888', 'code' => 'HR-888', 'stock' => 2, 'cost_price' => 10000, 'selling_price' => 20000],
            ['name' => 'Box DAP DH-F5', 'code' => 'DH-F5', 'stock' => 3, 'cost_price' => 23000, 'selling_price' => 50000],
            ['name' => 'Box Foomee QA44', 'code' => 'QA44', 'stock' => 1, 'cost_price' => 25000, 'selling_price' => 50000],
            ['name' => 'Box Music U19', 'code' => 'U-19', 'stock' => 24, 'cost_price' => 9000, 'selling_price' => 25000],
            ['name' => 'Box Stereo Extra Bass U223', 'code' => 'U223', 'stock' => 31, 'cost_price' => 9000, 'selling_price' => 25000],
            ['name' => 'Box Log On Type C LO-HF630C', 'code' => 'LO-HF630C', 'stock' => 1, 'cost_price' => 30000, 'selling_price' => 55000],
            ['name' => 'Box Log On Type C LO-HF620C', 'code' => 'LO-HF620C', 'stock' => 2, 'cost_price' => 28000, 'selling_price' => 50000],
            ['name' => 'Box Song Type C CK01', 'code' => 'CK01', 'stock' => 4, 'cost_price' => 28000, 'selling_price' => 50000],
            ['name' => 'Box Robot RE20', 'code' => 'RE20', 'stock' => 2, 'cost_price' => 15000, 'selling_price' => 30000],
            ['name' => 'Box Olike E11-new', 'code' => 'E11-new', 'stock' => 2, 'cost_price' => 15000, 'selling_price' => 30000],
            ['name' => 'Box Olike E201', 'code' => 'E201', 'stock' => 1, 'cost_price' => 17000, 'selling_price' => 40000],
            ['name' => 'Box Wellcomm HM-001', 'code' => 'HM-001', 'stock' => 5, 'cost_price' => 20000, 'selling_price' => 40000],
            ['name' => 'Box Fleco FL-56', 'code' => 'FL-56', 'stock' => 3, 'cost_price' => 15000, 'selling_price' => 30000],
            ['name' => 'Box Philips JB-08', 'code' => 'JB-08', 'stock' => 3, 'cost_price' => 15000, 'selling_price' => 30000],
            ['name' => 'Box Cuna E1C7P', 'code' => 'E1C7P', 'stock' => 4, 'cost_price' => 10000, 'selling_price' => 30000],
            ['name' => 'Box Sony EJ-123', 'code' => 'EJ-123', 'stock' => 3, 'cost_price' => 12000, 'selling_price' => 25000],
            ['name' => 'Box Sony XT-17', 'code' => 'XT-17', 'stock' => 1, 'cost_price' => 15000, 'selling_price' => 30000],
            ['name' => 'Box Sony XT-05', 'code' => 'XT-05', 'stock' => 1, 'cost_price' => 13000, 'selling_price' => 30000],
            ['name' => 'Box DAP DH-F11', 'code' => 'DH-F11', 'stock' => 5, 'cost_price' => 28000, 'selling_price' => 55000],
            ['name' => 'Box Meemax EJ-C301', 'code' => 'EJ-C301', 'stock' => 2, 'cost_price' => 30000, 'selling_price' => 55000],
            ['name' => 'Box Philips Xwin 811', 'code' => 'Xwin 811', 'stock' => 2, 'cost_price' => 17000, 'selling_price' => 35000],
            ['name' => 'Box DASE E-G2', 'code' => 'E-G2', 'stock' => 2, 'cost_price' => 17000, 'selling_price' => 40000],
            ['name' => 'Box JM TECH HR-JM', 'code' => 'HR-JM', 'stock' => 2, 'cost_price' => 25000, 'selling_price' => 50000],
            ['name' => 'Box Vivan Q19', 'code' => 'Q19', 'stock' => 4, 'cost_price' => 25000, 'selling_price' => 50000],
            ['name' => 'Box Cennotech HR-CT', 'code' => 'HR-CT', 'stock' => 4, 'cost_price' => 25000, 'selling_price' => 60000],
            ['name' => 'Box Headset Random HR-N', 'code' => 'HR-N', 'stock' => 5, 'cost_price' => 5000, 'selling_price' => 10000],
        ];

        // 5. Simpan Produk & Store Stocks (Tanpa Field Slug)
        foreach ($productsData as $item) {
            $product = Product::updateOrCreate(
                ['code' => $item['code']],
                [
                    'category_id'   => $headsetCategory->id,
                    'name'          => $item['name'],
                    'type'          => 'physical',
                    'stock'         => $item['stock'],
                    'cost_price'    => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'is_active'     => true,
                ]
            );

            // Buat / Update stok cabang untuk semua store
            foreach ($stores as $store) {
                StoreProductStock::updateOrCreate(
                    [
                        'store_id'   => $store->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'stock'         => $item['stock'],
                        'cost_price'    => $item['cost_price'],
                        'selling_price' => $item['selling_price'],
                    ]
                );
            }
        }
    }
}