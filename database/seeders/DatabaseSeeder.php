<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // MATIKAN TRUNCATE PRODUK
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Product::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            StoreSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
            AksesorisSeeder::class,
            PpobServerSeeder::class,
            
            // MATIKAN SEMUA SEEDER PRODUK & STOK AGAR HARGA TIDAK TERTIMPA
            // PulsaProductSeeder::class,
            // VoucherProductSeeder::class,
            // PerdanaProductSeeder::class,
            // EwalletBankProductSeeder::class,
            // AksesorisProductSeeder::class,
            // StoreProductStockSeeder::class,
        ]);
    }
}