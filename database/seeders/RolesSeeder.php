<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles based on production database
        Roles::create([
            'rolesName' => 'SuperAdmin',
            'keterangan' => 'Administrator dengan akses penuh',
            'deleteSts' => 0,
        ]);

        Roles::create([
            'rolesName' => 'Owner',
            'keterangan' => 'Kasir dengan akses transaksi',
            'deleteSts' => 0,
        ]);

        Roles::create([
            'rolesName' => 'Kasir',
            'keterangan' => 'Operator dengan akses terbatas',
            'deleteSts' => 0,
        ]);
    }
}
