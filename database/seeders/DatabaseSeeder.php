<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
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
            AksesorisSeeder::class, // Kategori level 1-2 khusus aksesoris

            // Seeder Produk Berdasarkan Rumpun Kategori
            PulsaProductSeeder::class,
            VoucherProductSeeder::class,
            PerdanaProductSeeder::class,
            EwalletBankProductSeeder::class,
            AksesorisProductSeeder::class,

            // Synchronize Stok Fisik per Cabang (Dijalankan Paling Akhir)
            StoreProductStockSeeder::class,
        ]);
    }
}