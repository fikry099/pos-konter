<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StoreProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreProductStockSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        StoreProductStock::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil HANYA produk bertipe FISIK (Voucher, Aksesoris, Perdana)
        $physicalProducts = Product::where('type', 'physical')->get();

        foreach ($physicalProducts as $product) {
            // Registrasi Stok untuk Cabang 1
            StoreProductStock::create([
                'store_id'   => 1,
                'product_id' => $product->id,
                'stock'      => $product->stock > 0 ? $product->stock : 10,
                'min_stock'  => $product->min_stock ?? 3,
            ]);

            // Registrasi Stok untuk Cabang 2
            StoreProductStock::create([
                'store_id'   => 2,
                'product_id' => $product->id,
                'stock'      => 5,
                'min_stock'  => 2,
            ]);
        }
    }
}