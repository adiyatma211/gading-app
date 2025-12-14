<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role_id' => 1, // Assuming role_id 1 is for admin
            'deleteSts' => '0',
        ]);

        // Create kasir user
        User::create([
            'name' => 'Kasir Gading',
            'username' => 'kasir',
            'password' => Hash::make('kasir123'),
            'role_id' => 2, // Assuming role_id 2 is for kasir
            'deleteSts' => '0',
        ]);

        // Create operator user
        User::create([
            'name' => 'Operator',
            'username' => 'operator',
            'password' => Hash::make('operator123'),
            'role_id' => 3, // Assuming role_id 3 is for operator
            'deleteSts' => '0',
        ]);
    }
}
