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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Kasir yang melayani retur
            $table->foreignId('shift_id')->constrained(); // Shift aktif saat retur
            $table->string('return_code')->unique(); // Format: RET-20260902-XXX
            $table->decimal('returned_total', 12, 2); // Nilai total barang yang dikembalikan customer
            $table->decimal('replacement_total', 12, 2); // Nilai total barang baru pengganti
            $table->decimal('price_difference', 12, 2); // Selisih (+ jika bayar lagi, - jika refund cash)
            $table->string('payment_method')->default('cash'); // cash / qris untuk selisih bayar
            $table->text('reason')->nullable(); // Alasan retur (misal: Salah Tipe HP)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};