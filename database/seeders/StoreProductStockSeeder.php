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

        $physicalProducts = Product::where('type', 'physical')->get();

        foreach ($physicalProducts as $product) {
            $targetMinStock = (int) ($product->min_stock ?? 5);

            // Cabang 1 (WannCell): Stok Normal
            StoreProductStock::create([
                'store_id'   => 1,
                'product_id' => $product->id,
                'stock'      => $product->stock > 0 ? $product->stock : $targetMinStock,
                'min_stock'  => $targetMinStock,
            ]);

            // Cabang 2 (ArkanCell): Stok dibuat di bawah min_stock agar mentriger reorder
            $lowStock = max(1, $targetMinStock - 3);

            StoreProductStock::create([
                'store_id'   => 2,
                'product_id' => $product->id,
                'stock'      => $lowStock, 
                'min_stock'  => $targetMinStock,
            ]);
        }
    }
}