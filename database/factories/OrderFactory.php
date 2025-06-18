<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 1000);
        $tax = $subtotal * 0.20; // 20% TVA
        $shipping = $this->faker->randomFloat(2, 5, 20);
        $total = $subtotal + $tax + $shipping;
        $commissionRate = 0.10; // 10% de commission
        $commissionAmount = $subtotal * $commissionRate;

        return [
            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
            'promoter_id' => $this->faker->boolean(20) ? User::factory() : null,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'commission_amount' => $commissionAmount,
            'promoter_commission' => $this->faker->boolean(20) ? $total * 0.05 : null, // 5% pour le promoteur
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'payment_method' => $this->faker->randomElement(['card', 'paypal', 'bank_transfer']),
            'shipping_address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
            'tracking_number' => $this->faker->boolean(70) ? 'TRK-' . strtoupper(Str::random(10)) : null,
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
    }
}
