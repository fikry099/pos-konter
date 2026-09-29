<?php

namespace Database\Seeders\Accessories;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreProductStock;

class CableDataProductSeeder extends Seeder
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

        // 2. Ambil atau Buat Sub-Kategori Cable Data & AUX
        $category = Category::firstOrCreate(
            ['slug' => 'cable-data-aux'],
            [
                'name'      => 'Cable Data & AUX',
                'parent_id' => $parentCategory->id
            ]
        );

        // 3. Rekapitulasi Data Barang
        $items = [
            // --- FOTO 1 ---
            ['name' => 'Top Micro Log-on', 'code' => 'LO-CB80', 'brand' => 'log-on', 'cost' => 6000, 'price' => 15000, 'stock' => 10, 'min_stock' => 20],
            ['name' => 'Top iPhone Inbox', 'code' => 'KT-03', 'brand' => 'inbox', 'cost' => 8000, 'price' => 20000, 'stock' => 16, 'min_stock' => 20],
            ['name' => 'Top Type C Ngen', 'code' => 'JCT2-01', 'brand' => 'ngen', 'cost' => 8000, 'price' => 20000, 'stock' => 10, 'min_stock' => 20],
            ['name' => 'Top Type C Inbox', 'code' => 'KT-03-C', 'brand' => 'inbox', 'cost' => 8000, 'price' => 20000, 'stock' => 18, 'min_stock' => 20],
            ['name' => 'Top Micro Inbox', 'code' => 'KT-03-M', 'brand' => 'inbox', 'cost' => 6000, 'price' => 15000, 'stock' => 15, 'min_stock' => 20],
            ['name' => 'Top C to IP Log-on', 'code' => 'LO-CB90', 'brand' => 'log-on', 'cost' => 18000, 'price' => 35000, 'stock' => 17, 'min_stock' => 20],
            ['name' => 'Top iPhone Olike', 'code' => 'D306L', 'brand' => 'olike', 'cost' => 8000, 'price' => 20000, 'stock' => 18, 'min_stock' => 20],
            ['name' => 'Top Micro Robot', 'code' => 'RBM100', 'brand' => 'robot', 'cost' => 6000, 'price' => 15000, 'stock' => 15, 'min_stock' => 20],
            ['name' => 'Top Type C Olike', 'code' => 'D307C', 'brand' => 'olike', 'cost' => 9000, 'price' => 25000, 'stock' => 4, 'min_stock' => 20],
            ['name' => 'Top Micro Olike', 'code' => 'D306M', 'brand' => 'olike', 'cost' => 6000, 'price' => 15000, 'stock' => 18, 'min_stock' => 20],
            ['name' => 'Top Type C Robot', 'code' => 'RBC100', 'brand' => 'robot', 'cost' => 8000, 'price' => 20000, 'stock' => 12, 'min_stock' => 20],
            ['name' => 'Top C to C Log-on', 'code' => 'LO-CB90-CC', 'brand' => 'log-on', 'cost' => 15000, 'price' => 30000, 'stock' => 18, 'min_stock' => 20],
            ['name' => 'Top Type C Log-on', 'code' => 'LO-CB90-C', 'brand' => 'log-on', 'cost' => 9000, 'price' => 25000, 'stock' => 17, 'min_stock' => 20],
            ['name' => 'Top Type C Oraimo', 'code' => 'OCD-121C', 'brand' => 'oraimo', 'cost' => 13000, 'price' => 30000, 'stock' => 10, 'min_stock' => 20],
            ['name' => 'Top Micro Oraimo', 'code' => 'OCD-114MJ', 'brand' => 'oraimo', 'cost' => 8000, 'price' => 20000, 'stock' => 2, 'min_stock' => 10],
            ['name' => 'Top Type C Oraimo', 'code' => 'OCD-114CJ', 'brand' => 'oraimo', 'cost' => 9000, 'price' => 25000, 'stock' => 4, 'min_stock' => 10],
            ['name' => 'Top Micro CAMP', 'code' => 'CAMP-01', 'brand' => 'camp', 'cost' => 8000, 'price' => 20000, 'stock' => 10, 'min_stock' => 20],
            ['name' => 'Top Micro Rocket', 'code' => 'RCM-100', 'brand' => 'roket', 'cost' => 8000, 'price' => 20000, 'stock' => 20, 'min_stock' => 30],
            ['name' => 'Top Type C Rocket', 'code' => 'RCC-100', 'brand' => 'roket', 'cost' => 14000, 'price' => 30000, 'stock' => 20, 'min_stock' => 25],
            ['name' => 'Top Micro 2M Rocket', 'code' => 'M-2M-01', 'brand' => 'roket', 'cost' => 18000, 'price' => 35000, 'stock' => 4, 'min_stock' => 20],
            ['name' => 'Top Micro CAMP 02', 'code' => 'CAMP-02', 'brand' => 'camp', 'cost' => 8000, 'price' => 20000, 'stock' => 9, 'min_stock' => 20],
            ['name' => 'Top C to C Wellcomm', 'code' => 'W-EMC', 'brand' => 'wellcomm', 'cost' => 20000, 'price' => 40000, 'stock' => 4, 'min_stock' => 20],
            ['name' => 'Top iPhone Oraimo', 'code' => 'OCD-114UJ', 'brand' => 'oraimo', 'cost' => 10000, 'price' => 25000, 'stock' => 3, 'min_stock' => 20],
            ['name' => 'Top Type C 2M Rocket', 'code' => 'RCC-200', 'brand' => 'roket', 'cost' => 15000, 'price' => 40000, 'stock' => 8, 'min_stock' => 20],

            // --- FOTO 2 ---
            ['name' => 'Top iPhone Rocket', 'code' => 'RCL-100', 'brand' => 'roket', 'cost' => 18000, 'price' => 30000, 'stock' => 18, 'min_stock' => 20],
            ['name' => 'Top Micro Ngen', 'code' => 'JCM2-01', 'brand' => 'ngen', 'cost' => 6000, 'price' => 15000, 'stock' => 7, 'min_stock' => 20],
            ['name' => 'Top C to C Ori SAM', 'code' => 'C-ORI-SAM', 'brand' => 'sam', 'cost' => 28000, 'price' => 60000, 'stock' => 25, 'min_stock' => 25],
            ['name' => 'Top Micro Rexi', 'code' => 'TC20-M', 'brand' => 'rexi', 'cost' => 6000, 'price' => 15000, 'stock' => 10, 'min_stock' => 10],
            ['name' => 'Top Micro Robot RGM100', 'code' => 'RGM100', 'brand' => 'robot', 'cost' => 8000, 'price' => 20000, 'stock' => 11, 'min_stock' => 20],
            ['name' => 'Top Micro Oraimo OCD-114U', 'code' => 'OCD-114U', 'brand' => 'oraimo', 'cost' => 8000, 'price' => 20000, 'stock' => 15, 'min_stock' => 20],
            ['name' => 'Top Micro Minimo', 'code' => 'DGC01S', 'brand' => 'minimo', 'cost' => 5000, 'price' => 10000, 'stock' => 2, 'min_stock' => 10],
            ['name' => 'Top Type C Minimo', 'code' => 'DGC03', 'brand' => 'minimo', 'cost' => 9000, 'price' => 25000, 'stock' => 3, 'min_stock' => 10],
            ['name' => 'Top C to C Rocket', 'code' => 'PCC-100', 'brand' => 'roket', 'cost' => 15000, 'price' => 30000, 'stock' => 18, 'min_stock' => 20],
            ['name' => 'Top Type C Fantech', 'code' => 'PCL002C', 'brand' => 'fantech', 'cost' => 8000, 'price' => 20000, 'stock' => 13, 'min_stock' => 20],
            ['name' => 'Top Type C Pendek', 'code' => 'PN-C-01', 'brand' => 'roket', 'cost' => 9000, 'price' => 25000, 'stock' => 13, 'min_stock' => 20],
            ['name' => 'Top Micro Pendek DAP', 'code' => 'DPM25', 'brand' => 'dap', 'cost' => 6000, 'price' => 15000, 'stock' => 32, 'min_stock' => 32],
            ['name' => 'Top Micro Pendek', 'code' => 'PN-MIK-01', 'brand' => 'roket', 'cost' => 2000, 'price' => 5000, 'stock' => 75, 'min_stock' => 75],
            ['name' => 'Top Micro Robot RCO M100', 'code' => 'RCO-M100', 'brand' => 'robot', 'cost' => 8000, 'price' => 20000, 'stock' => 20, 'min_stock' => 20],
            ['name' => 'Box Type C Onesam', 'code' => 'OS-25', 'brand' => 'onesam', 'cost' => 38000, 'price' => 55000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Type C Home', 'code' => 'OS-226', 'brand' => 'home', 'cost' => 48000, 'price' => 80000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Mi', 'code' => 'P-31C', 'brand' => 'mi', 'cost' => 19000, 'price' => 35000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C SAM', 'code' => 'P-31C-SAM', 'brand' => 'sam', 'cost' => 18000, 'price' => 35000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Well', 'code' => 'TC-USB24F', 'brand' => 'wellcomm', 'cost' => 45000, 'price' => 75000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box HK Micro', 'code' => 'HKTC-D09', 'brand' => 'hk', 'cost' => 40000, 'price' => 60000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Fleco', 'code' => 'TA-200', 'brand' => 'fleco', 'cost' => 12000, 'price' => 25000, 'stock' => 2, 'min_stock' => 2],

            // --- FOTO 3 ---
            ['name' => 'Box Kabel 2 Adaptor', 'code' => 'JH-006', 'brand' => 'other', 'cost' => 28000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box IP Flat', 'code' => 'PO27-MAT', 'brand' => 'other', 'cost' => 22000, 'price' => 50000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Micro ZBAK', 'code' => 'ZB-07', 'brand' => 'zbak', 'cost' => 25000, 'price' => 45000, 'stock' => 5, 'min_stock' => 5],
            ['name' => 'Box Micro', 'code' => 'I9000', 'brand' => 'other', 'cost' => 10000, 'price' => 20000, 'stock' => 1, 'min_stock' => 4],
            ['name' => 'Box Audio Jac Adaptor', 'code' => 'KAB-JAC', 'brand' => 'other', 'cost' => 15000, 'price' => 25000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box AUX DAP', 'code' => 'D-AV03', 'brand' => 'dap', 'cost' => 15000, 'price' => 30000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box AUX Philips', 'code' => 'AUX-PHI', 'brand' => 'philips', 'cost' => 7000, 'price' => 20000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box AUX Hisau', 'code' => 'AUX-HISAU', 'brand' => 'hisau', 'cost' => 4500, 'price' => 15000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Robot', 'code' => 'RJC100', 'brand' => 'robot', 'cost' => 37000, 'price' => 65000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C Robot', 'code' => 'RZCC100', 'brand' => 'robot', 'cost' => 46000, 'price' => 70000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box C to C Robot 120', 'code' => 'RSPD120', 'brand' => 'robot', 'cost' => 45000, 'price' => 75000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C Robot 60W', 'code' => 'RCC60W', 'brand' => 'robot', 'cost' => 33000, 'price' => 60000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Micro Vivan', 'code' => 'USM100S', 'brand' => 'vivan', 'cost' => 30000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro 2M Vivan', 'code' => 'VDM200', 'brand' => 'vivan', 'cost' => 31000, 'price' => 65000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Vivan', 'code' => 'BTK-LS', 'brand' => 'vivan', 'cost' => 35000, 'price' => 60000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C Vivan', 'code' => 'VDCC120', 'brand' => 'vivan', 'cost' => 50000, 'price' => 80000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro Wellcomm', 'code' => 'KAB-MIC-WELL', 'brand' => 'wellcomm', 'cost' => 20000, 'price' => 45000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box IP Wellcomm', 'code' => 'KAB-IP-WELL', 'brand' => 'wellcomm', 'cost' => 25000, 'price' => 50000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box C Wellcomm', 'code' => 'KAB-C-WELL', 'brand' => 'wellcomm', 'cost' => 30000, 'price' => 50000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box C RAPA', 'code' => 'CH4141', 'brand' => 'rapa', 'cost' => 45000, 'price' => 70000, 'stock' => 5, 'min_stock' => 5],

            // --- FOTO 4 ---
            ['name' => 'Box Iset Micro Well', 'code' => 'MIC-C-WELL', 'brand' => 'wellcomm', 'cost' => 10000, 'price' => 25000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box AUX Kin', 'code' => 'KY-28', 'brand' => 'kin', 'cost' => 10000, 'price' => 25000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box AUX Vivan', 'code' => 'AUX01', 'brand' => 'vivan', 'cost' => 32000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C DAP', 'code' => 'D-PD120', 'brand' => 'dap', 'cost' => 30000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C DAP', 'code' => 'DCBT100', 'brand' => 'dap', 'cost' => 13000, 'price' => 30000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box iPhone DAP', 'code' => 'DAP-IP', 'brand' => 'dap', 'cost' => 28000, 'price' => 45000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box Micro Schaffsen', 'code' => 'SCH-036', 'brand' => 'schaffsen', 'cost' => 13000, 'price' => 25000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box Micro Foomee', 'code' => 'FD100-M', 'brand' => 'foomee', 'cost' => 19000, 'price' => 30000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box iPhone Foomee', 'code' => 'FD100-L', 'brand' => 'foomee', 'cost' => 25000, 'price' => 40000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Micro Foomee NA12', 'code' => 'NA12', 'brand' => 'foomee', 'cost' => 15000, 'price' => 25000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Micro Vidvie', 'code' => 'CB031V', 'brand' => 'vidvie', 'cost' => 12000, 'price' => 25000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box Type C USAMS', 'code' => 'US-SJ200', 'brand' => 'usams', 'cost' => 15000, 'price' => 30000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Type C Rexi', 'code' => 'KE01-M', 'brand' => 'rexi', 'cost' => 24000, 'price' => 45000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box C to C Ugreen', 'code' => 'CTI-20', 'brand' => 'ugreen', 'cost' => 38000, 'price' => 50000, 'stock' => 5, 'min_stock' => 5],
            ['name' => 'Box Type C B2X', 'code' => 'B2X-C', 'brand' => 'b2x', 'cost' => 15000, 'price' => 25000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box iPhone Wellcomm', 'code' => 'P-LM-WELL', 'brand' => 'wellcomm', 'cost' => 24000, 'price' => 45000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Type C Mikan', 'code' => 'KAB-C-H', 'brand' => 'mikan', 'cost' => 19000, 'price' => 35000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box Type C Memax B402', 'code' => 'SX-B402', 'brand' => 'memax', 'cost' => 32000, 'price' => 55000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box Type C Memax B101', 'code' => 'SX-B101-A04', 'brand' => 'memax', 'cost' => 25000, 'price' => 45000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Micro Memax B501', 'code' => 'SX-B501', 'brand' => 'memax', 'cost' => 25000, 'price' => 55000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Type C Game', 'code' => 'KAB-C-GC', 'brand' => 'other', 'cost' => 5000, 'price' => 15000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box Micro Foomee NP10S', 'code' => 'NP10S', 'brand' => 'foomee', 'cost' => 10000, 'price' => 20000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Type C Hikaru', 'code' => 'KAB-C-H2', 'brand' => 'hikaru', 'cost' => 18000, 'price' => 35000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box iPhone Rexi', 'code' => 'KC01-LX', 'brand' => 'rexi', 'cost' => 30000, 'price' => 50000, 'stock' => 6, 'min_stock' => 6],
            ['name' => 'Box Type C Vivo', 'code' => 'KAB-VC', 'brand' => 'vivo', 'cost' => 20000, 'price' => 35000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Micro Viral', 'code' => 'MIC-VIRAL', 'brand' => 'viral', 'cost' => 14000, 'price' => 25000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box iPhone Memax', 'code' => 'SX-B202', 'brand' => 'memax', 'cost' => 35000, 'price' => 55000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box iPhone Onesam', 'code' => 'OS-A25', 'brand' => 'onesam', 'cost' => 15000, 'price' => 30000, 'stock' => 2, 'min_stock' => 2],

            // --- FOTO 5 ---
            ['name' => 'Box Micro Onesam', 'code' => 'OS-A25-M', 'brand' => 'onesam', 'cost' => 13000, 'price' => 25000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box C to IP Oraimo 710', 'code' => 'OCD-710CL', 'brand' => 'oraimo', 'cost' => 66000, 'price' => 95000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box C to IP Oraimo 114', 'code' => 'OCD-114CL', 'brand' => 'oraimo', 'cost' => 40000, 'price' => 60000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C Oraimo', 'code' => 'OCD-1525CC', 'brand' => 'oraimo', 'cost' => 34000, 'price' => 55000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Oraimo', 'code' => 'OCD-152L', 'brand' => 'oraimo', 'cost' => 18000, 'price' => 50000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Type C Oraimo', 'code' => 'OCD-152C', 'brand' => 'oraimo', 'cost' => 19000, 'price' => 50000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Type C Oraimo C63', 'code' => 'OCD-C63', 'brand' => 'oraimo', 'cost' => 20000, 'price' => 40000, 'stock' => 5, 'min_stock' => 5],
            ['name' => 'Box ugreen iPhone', 'code' => 'UGL', 'brand' => 'ugreen', 'cost' => 175000, 'price' => 175000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Audio Adapter Pinzy C', 'code' => '610', 'brand' => 'pinzy', 'cost' => 18000, 'price' => 35000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Audio Adapter IP Pinzy', 'code' => '58', 'brand' => 'pinzy', 'cost' => 18000, 'price' => 40000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Oraimo 3 in 1 Kabel', 'code' => 'OCD-X101', 'brand' => 'oraimo', 'cost' => 55000, 'price' => 75000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box iPhone Olike D401', 'code' => 'D401L', 'brand' => 'olike', 'cost' => 25000, 'price' => 60000, 'stock' => 6, 'min_stock' => 6],
            ['name' => 'Box Micro Olike D205M', 'code' => 'D205M', 'brand' => 'olike', 'cost' => 15000, 'price' => 30000, 'stock' => 4, 'min_stock' => 4],
            ['name' => 'Box Micro Tecnix', 'code' => 'CBL-833-M', 'brand' => 'tecnix', 'cost' => 11000, 'price' => 45000, 'stock' => 10, 'min_stock' => 10],
            ['name' => 'Box Type C Olike D205C', 'code' => 'D205C', 'brand' => 'olike', 'cost' => 30000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Redskul', 'code' => 'RSDC100', 'brand' => 'redskul', 'cost' => 11000, 'price' => 50000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike D401L', 'code' => 'D401L-2', 'brand' => 'olike', 'cost' => 38000, 'price' => 55000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box C to C Olike', 'code' => 'D103CC', 'brand' => 'olike', 'cost' => 50000, 'price' => 75000, 'stock' => 1, 'min_stock' => 1],

            // --- FOTO 6 ---
            ['name' => 'Box C to C Olike D102', 'code' => 'D102CC', 'brand' => 'olike', 'cost' => 35000, 'price' => 65000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C Olike D105', 'code' => 'D105CC', 'brand' => 'olike', 'cost' => 42000, 'price' => 75000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box AUX Olike ATA1', 'code' => 'ATA1', 'brand' => 'olike', 'cost' => 25000, 'price' => 45000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Micro 2M Olike', 'code' => 'D604M', 'brand' => 'olike', 'cost' => 27000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Tecnix', 'code' => 'CBL-833-I', 'brand' => 'tecnix', 'cost' => 25000, 'price' => 50000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box iPhone Olike D602', 'code' => 'D602C', 'brand' => 'olike', 'cost' => 18000, 'price' => 35000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike D603', 'code' => 'D603C', 'brand' => 'olike', 'cost' => 22000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike 2M', 'code' => 'D604L', 'brand' => 'olike', 'cost' => 21000, 'price' => 55000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike D108L', 'code' => 'D108L', 'brand' => 'olike', 'cost' => 28000, 'price' => 50000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike D109L', 'code' => 'D109L', 'brand' => 'olike', 'cost' => 37000, 'price' => 55000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike D109C', 'code' => 'D109C', 'brand' => 'olike', 'cost' => 23000, 'price' => 60000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box iPhone Olike D501L', 'code' => 'D501L', 'brand' => 'olike', 'cost' => 32000, 'price' => 60000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box Type C Olike C301', 'code' => 'C301', 'brand' => 'olike', 'cost' => 40000, 'price' => 70000, 'stock' => 1, 'min_stock' => 2],
            ['name' => 'Box C to C ZBOX', 'code' => 'ZX-650', 'brand' => 'zbox', 'cost' => 25000, 'price' => 45000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box iPhone Luna CB-2AS', 'code' => 'CB-2AS-IP', 'brand' => 'luna', 'cost' => 27000, 'price' => 50000, 'stock' => 5, 'min_stock' => 5],
            ['name' => 'Box iPhone Luna CB-2A7', 'code' => 'CB-2A7-IP', 'brand' => 'luna', 'cost' => 24000, 'price' => 50000, 'stock' => 5, 'min_stock' => 5],
            ['name' => 'Box iPhone Luna CB-2DC', 'code' => 'CB-2DC-IP', 'brand' => 'luna', 'cost' => 19000, 'price' => 35000, 'stock' => 2, 'min_stock' => 2],
            ['name' => 'Box Micro Luna', 'code' => 'CB-2AS-M', 'brand' => 'luna', 'cost' => 23000, 'price' => 35000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Type C Luna', 'code' => 'CB-2DC-C', 'brand' => 'luna', 'cost' => 18000, 'price' => 30000, 'stock' => 1, 'min_stock' => 1],
            ['name' => 'Box Type C Luna CB-2APC', 'code' => 'CB-2APC', 'brand' => 'luna', 'cost' => 18000, 'price' => 35000, 'stock' => 3, 'min_stock' => 3],
            ['name' => 'Box Type C Luna CB-2A7', 'code' => 'CB-2A7-C', 'brand' => 'luna', 'cost' => 25000, 'price' => 45000, 'stock' => 6, 'min_stock' => 6],
            ['name' => 'Box Type C Luna CB-2AQ', 'code' => 'CB-2AQ', 'brand' => 'luna', 'cost' => 21000, 'price' => 45000, 'stock' => 6, 'min_stock' => 6],
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
                    'stock'         => 0, // Master stock dibuat 0
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