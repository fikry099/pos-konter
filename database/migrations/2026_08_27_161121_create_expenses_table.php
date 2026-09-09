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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade'); // Cabang lokasi pengeluaran
            $table->foreignId('shift_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
            $table->string('category')->default('operational');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
