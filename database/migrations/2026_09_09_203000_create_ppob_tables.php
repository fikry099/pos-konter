<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk menyimpan data server / aplikasi (Propana, Seabank, dll)
        if (!Schema::hasTable('ppob_servers')) {
            Schema::create('ppob_servers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->decimal('balance', 15, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Tabel untuk menyimpan riwayat isi ulang saldo (deposit)
        if (!Schema::hasTable('ppob_deposits')) {
            Schema::create('ppob_deposits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ppob_server_id')->constrained('ppob_servers')->onDelete('cascade');
                $table->decimal('amount', 15, 2);
                $table->string('notes')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ppob_deposits');
        Schema::dropIfExists('ppob_servers');
    }
};