<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\produk_bahan>
 */
class ProdukBahanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'produk_id' => \App\Models\Produk::factory(),
            'nama_bahan' => $this->faker->words(2, true),
            'harga_per_meter' => $this->faker->randomFloat(2, 1000, 50000),
            'diskon' => $this->faker->randomFloat(2, 0, 10000),
            'total_harga' => $this->faker->randomFloat(2, 10000, 500000),
        ];
    }
}
