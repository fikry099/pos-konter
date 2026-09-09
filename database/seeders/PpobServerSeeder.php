<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PpobServer;

class PpobServerSeeder extends Seeder
{
    public function run(): void
    {
        $servers = [
            'Propana',
            'Mitra Shopee',
            'SeaBank',
            'Digipos',
            'Rita',
            'Dompul',
            'Simpel'
        ];

        foreach ($servers as $serverName) {
            PpobServer::firstOrCreate(
                ['name' => $serverName],
                ['balance' => 0, 'is_active' => true]
            );
        }
    }
}