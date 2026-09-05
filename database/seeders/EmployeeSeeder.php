<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Karyawan Operasional Toko (Non-Owner)
        $employees = [
            [
                'name'     => 'Suryadi',
                'email'    => 'sur@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Sekar',
                'email'    => 'sekar@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Sonia',
                'email'    => 'sonia@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Monica',
                'email'    => 'monica@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Fara',
                'email'    => 'fara@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Desi',
                'email'    => 'desi@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Eka',
                'email'    => 'eka@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
            [
                'name'     => 'Zhara',
                'email'    => 'zhara@wanncell.com',
                'password' => bcrypt('password'),
                'role'     => 'karyawan',
            ],
        ];

        foreach ($employees as $employee) {
            User::updateOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}