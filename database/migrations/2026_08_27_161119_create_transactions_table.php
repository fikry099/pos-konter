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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade'); // Cabang lokasi transaksi
            $table->string('invoice_code')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('shift_id')->constrained();
            $table->decimal('total_cost', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->decimal('total_profit', 12, 2);
            $table->decimal('pay_amount', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->string('payment_method')->default('cash');
            $table->text('payment_proof')->nullable();

            /* --- FITUR PEMBATALAN TRANSAKSI (DITAMBAHKAN) --- */
            $table->string('status')->default('completed'); // completed / cancelled
            $table->text('cancel_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};