<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $this->call([
            StoreSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
            PpobServerSeeder::class,

            PulsaProductSeeder::class,
            VoucherProductSeeder::class,
            PerdanaProductSeeder::class,
            EwalletBankProductSeeder::class,
            StoreProductStockSeeder::class,


            \Database\Seeders\accessories\CableDataProductSeeder::class,
            \Database\Seeders\accessories\ChargerSetProductSeeder::class,
            \Database\Seeders\accessories\AdapterHeadProductSeeder::class,
            \Database\Seeders\accessories\HeadsetEarphoneProductSeeder::class,
            \Database\Seeders\accessories\StorageProductSeeder::class,
            \Database\Seeders\accessories\PowerbankProductSeeder::class,
        ]);
    }
}