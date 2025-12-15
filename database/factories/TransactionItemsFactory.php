<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\transaction_items>
 */
class TransactionItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => \App\Models\transactions::factory(),
            'tipe_produk_id' => \App\Models\Produk::factory(),
            'panjang' => $this->faker->randomFloat(2, 10, 100),
            'lebar' => $this->faker->randomFloat(2, 10, 100),
            'qty' => $this->faker->optional(0.5)->numberBetween(1, 100),
            'sisi' => $this->faker->randomElement([1, 2]),
            'laminasi' => $this->faker->boolean(),
            'harga_per_meter' => $this->faker->randomFloat(2, 1000, 50000),
            'diskon_barang' => $this->faker->randomFloat(2, 0, 10000),
            'total_harga' => $this->faker->randomFloat(2, 10000, 500000),
            'keterangan' => $this->faker->sentence(),
            'createdBy' => 'Test User',
        ];
    }
}
