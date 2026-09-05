<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Pulsa Tsel 10k, Voucher Three 5GB, Casing, dll
            $table->string('code')->nullable()->unique(); // Barcode barang fisik (opsional)
            $table->string('image')->nullable();
            $table->string('type')->default('digital'); // 'physical' atau 'digital'
            $table->decimal('cost_price', 12, 2); // Harga Modal / HPP
            $table->decimal('selling_price', 12, 2); // Harga Jual ke Pembeli
            $table->integer('stock')->default(0); // Berlaku untuk barang fisik / voucher
            $table->integer('min_stock')->default(0); // Fitur Reorder Point (Restok Warning)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
