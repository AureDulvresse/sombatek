<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement(['Basic', 'Pro', 'Enterprise']),
            'stripe_id' => 'sub_' . strtoupper(uniqid()),
            'stripe_status' => $this->faker->randomElement(['active', 'past_due', 'canceled']),
            'stripe_price' => 'price_' . strtoupper(uniqid()),
            'quantity' => 1,
            'trial_ends_at' => $this->faker->optional()->dateTimeBetween('now', '+14 days'),
            'ends_at' => $this->faker->optional()->dateTimeBetween('+1 month', '+1 year'),
            'type' => $this->faker->randomElement(['monthly', 'yearly']),
            'amount' => $this->faker->randomFloat(2, 29.99, 199.99),
            'features' => json_encode([
                'products' => $this->faker->randomElement([100, 500, 'unlimited']),
                'storage' => $this->faker->randomElement(['5GB', '20GB', '100GB']),
                'support' => $this->faker->randomElement(['email', 'priority', '24/7']),
                'analytics' => $this->faker->randomElement(['basic', 'advanced', 'premium'])
            ])
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'stripe_status' => 'active',
            'trial_ends_at' => null,
            'ends_at' => null
        ]);
    }

    public function trialing(): static
    {
        return $this->state(fn(array $attributes) => [
            'stripe_status' => 'trialing',
            'trial_ends_at' => now()->addDays(14),
            'ends_at' => null
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn(array $attributes) => [
            'stripe_status' => 'canceled',
            'ends_at' => now()->addDays(30)
        ]);
    }
}
