<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnershipFactory extends Factory
{
   public function definition(): array
   {
      $startDate = $this->faker->dateTimeBetween('now', '+1 month');
      $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');

      return [
         'promoter_id' => User::factory()->state(['role' => 'promoter']),
         'shop_id' => Shop::factory(),
         'code' => 'PART-' . strtoupper(uniqid()),
         'commission_rate' => $this->faker->randomFloat(2, 5, 15),
         'status' => $this->faker->randomElement(['pending', 'active', 'suspended', 'terminated']),
         'start_date' => $startDate,
         'end_date' => $endDate,
         'terms' => json_encode([
            'minimum_sales' => $this->faker->numberBetween(3000, 10000),
            'payment_terms' => $this->faker->randomElement(['net30', 'net60', 'immediate']),
            'exclusivity' => $this->faker->boolean(),
            'territory' => $this->faker->randomElement(['local', 'national', 'international'])
         ]),
         'settings' => json_encode([
            'auto_approve' => $this->faker->boolean(),
            'notification_email' => true,
            'reporting_frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly'])
         ])
      ];
   }

   public function active(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'active',
         'start_date' => now(),
         'end_date' => now()->addMonths(6),
      ]);
   }

   public function pending(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'pending',
         'start_date' => now()->addDays(7),
         'end_date' => now()->addMonths(6),
      ]);
   }

   public function suspended(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'suspended',
         'start_date' => now()->subMonths(3),
         'end_date' => now()->addMonths(3),
      ]);
   }
}
