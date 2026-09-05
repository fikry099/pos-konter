<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Akun Owner (Akses Global - Semua Cabang)
        User::create([
            'name'     => 'Owner',
            'email'    => 'Onynovia27@gmail.com',
            'password' => Hash::make('password123'),
            'role'     => 'owner',
            'store_id' => null,
        ]);

        // 2. Akun Kasir Cabang 1
        User::create([
            'name'     => 'WannCell',
            'email'    => 'WannCell@email.com',
            'password' => Hash::make('password123'),
            'role'     => 'cabang',
            'store_id' => 1,
        ]);

        // 3. Akun Kasir Cabang 2
        User::create([
            'name'     => 'Arkancell',
            'email'    => 'Arkancell@email.com',
            'password' => Hash::make('password123'),
            'role'     => 'cabang',
            'store_id' => 2,
        ]);
    }
}