<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\transactions>
 */
class TransactionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => \App\Models\customers::factory(),
            'subtotal' => $this->faker->randomFloat(2, 10000, 500000),
            'total' => $this->faker->randomFloat(2, 10000, 500000),
            'biaya_desain' => $this->faker->randomFloat(2, 0, 100000),
            'diskon' => $this->faker->randomFloat(2, 0, 50000),
            'dp' => $this->faker->randomFloat(2, 0, 100000),
            'metode_pembayaran' => $this->faker->randomElement(['cash', 'transfer', 'debit']),
            'status_pembayaran' => $this->faker->randomElement(['pending', 'dp', 'lunas']),
            'tanggal_transaksi' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tanggal_ambil' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month'),
            'nomor_faktur' => 'GD-MMT-' . str_pad($this->faker->unique()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd'),
            'createdBy' => 'Test User',
        ];
    }
}
