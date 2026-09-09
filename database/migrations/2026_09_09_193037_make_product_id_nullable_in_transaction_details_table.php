<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
{
    // Biarkan kosong atau ubah agar tidak memaksa NOT NULL jika ada data null
    Schema::table('transaction_details', function (Blueprint $table) {
        $table->unsignedBigInteger('product_id')->nullable()->change();
    });
}
};
