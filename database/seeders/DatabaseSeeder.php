<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UsersSeeder::class,
            // Add other seeders here as needed
            // CustomersSeeder::class,
            // ProdukSeeder::class,
            // ProdukBahanSeeder::class,
            // TransactionsSeeder::class,
            // TransactionItemsSeeder::class,
            // HistorynotaSeeder::class,
            // HistoyPaymentSeeder::class,
        ]);
    }
}
