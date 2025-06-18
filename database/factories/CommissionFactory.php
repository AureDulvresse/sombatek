<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);
        $promoterAmount = $amount * 0.5; // 50% pour le promoteur
        $platformAmount = $amount - $promoterAmount;

        return [
            'order_id' => Order::factory(),
            'promoter_id' => User::factory()->state(['role' => 'promoter']),
            'shop_id' => Shop::factory(),
            'amount' => $amount,
            'promoter_amount' => $promoterAmount,
            'platform_amount' => $platformAmount,
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'paid_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'payment_reference' => $this->faker->optional()->uuid,
            'notes' => $this->faker->optional()->sentence
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
            'payment_reference' => null
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
            'paid_at' => now()->subDays(rand(1, 30)),
            'payment_reference' => $this->faker->uuid
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
            'paid_at' => null,
            'payment_reference' => null
        ]);
    }
}
