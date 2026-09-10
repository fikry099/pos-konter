<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom harga ke tabel store_product_stocks
        Schema::table('store_product_stocks', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->after('min_stock');
            $table->decimal('selling_price', 15, 2)->nullable()->after('cost_price');
        });

        // 2. Salin data harga lama dari tabel 'products' ke 'store_product_stocks' agar data tidak kosong
        DB::statement("
            UPDATE store_product_stocks 
            JOIN products ON store_product_stocks.product_id = products.id 
            SET store_product_stocks.cost_price = products.cost_price,
                store_product_stocks.selling_price = products.selling_price
        ");
    }

    public function down(): void
    {
        Schema::table('store_product_stocks', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'selling_price']);
        });
    }
};