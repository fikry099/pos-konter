<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tabel products secara aman sebelum seeder running
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            StoreSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
            AksesorisSeeder::class,
            PpobServerSeeder::class,
            // 1. Buat Data Master Produk Terlebih Dahulu
            PulsaProductSeeder::class,
            VoucherProductSeeder::class,
            PerdanaProductSeeder::class,
            EwalletBankProductSeeder::class,
            AksesorisProductSeeder::class,

            // 2. Generasi Stok Fisik Cabang Berdasarkan Produk yang Sudah Ada
            StoreProductStockSeeder::class,
        ]);
    }
}