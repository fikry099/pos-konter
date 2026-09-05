<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Store::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Store::create([
            'id'        => 1,
            'name'      => 'WANNCELL',
            'code'      => 'WNC-01',
            'address'   => 'Jl. Urip Sumoharjo, Gose, Bantul, Yogyakarta',
            'phone'     => '081234567890',
            'is_active' => true,
        ]);

        Store::create([
            'id'        => 2,
            'name'      => 'ARKANCELL',
            'code'      => 'WNC-02',
            'address'   => 'Jl. Ir.H.Juanda, Trirenggo, Bantul, Yogyakarta',
            'phone'     => '081987654321',
            'is_active' => true,
        ]);
    }
}