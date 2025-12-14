<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HargaProdukNew>
 */
class HargaProdukNewFactory extends Factory
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
            'harga' => $this->faker->randomFloat(2, 1000, 50000),
            'min_qty' => $this->faker->optional(0.5)->numberBetween(1, 100),
            'max_qty' => $this->faker->optional(0.5)->numberBetween(101, 500),
            'sisi' => $this->faker->randomElement([1, 2]),
            'diskon' => $this->faker->randomFloat(2, 0, 10000),
            'laminasi' => $this->faker->boolean(),
        ];
    }
}
