<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
   protected $model = Cart::class;

   public function definition(): array
   {
      $subtotal = fake()->randomFloat(2, 10, 1000);
      $tax = $subtotal * 0.20; // TVA 20%
      $shipping = fake()->randomFloat(2, 5, 20);
      $total = $subtotal + $tax + $shipping;

      return [
         'user_id' => User::factory(),
         'session_id' => fake()->uuid(),
         'status' => fake()->randomElement(['active', 'abandoned', 'converted']),
         'total_items' => fake()->numberBetween(1, 10),
         'subtotal' => $subtotal,
         'tax' => $tax,
         'shipping' => $shipping,
         'total' => $total,
         'notes' => fake()->optional()->sentence(),
         'expires_at' => fake()->dateTimeBetween('now', '+30 days'),
         'last_activity' => fake()->dateTimeBetween('-1 hour', 'now')
      ];
   }

   public function active(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'active',
         'expires_at' => fake()->dateTimeBetween('now', '+30 days'),
         'last_activity' => fake()->dateTimeBetween('-1 hour', 'now')
      ]);
   }

   public function abandoned(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'abandoned',
         'last_activity' => fake()->dateTimeBetween('-2 days', '-1 day')
      ]);
   }

   public function converted(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'converted',
         'last_activity' => fake()->dateTimeBetween('-1 day', 'now')
      ]);
   }

   public function guest(): static
   {
      return $this->state(fn(array $attributes) => [
         'user_id' => null,
         'session_id' => fake()->uuid()
      ]);
   }
}
