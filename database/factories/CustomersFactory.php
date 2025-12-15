<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\customers>
 */
class CustomersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'jenis_pelanggan' => $this->faker->randomElement(['regular', 'premium']),
            'alamat' => $this->faker->address(),
            'createdBy' => 'Test User',
        ];
    }
}
