<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_produk' => $this->faker->words(3, true),
            'tipe_produk' => $this->faker->randomElement(['per_meter', 'tiered', 'flat', 'custom']),
            'status' => 1,
        ];
    }
}
