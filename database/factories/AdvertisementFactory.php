<?php

namespace Database\Factories;

use App\Models\Advertisement;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisementFactory extends Factory
{
   protected $model = Advertisement::class;

   public function definition(): array
   {
      $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
      $endDate = fake()->dateTimeBetween($startDate, '+3 months');

      return [
         'shop_id' => Shop::factory(),
         'title' => fake()->sentence(3),
         'description' => fake()->paragraph(),
         'image' => fake()->imageUrl(1200, 400, 'advertisement'),
         'link' => fake()->url(),
         'position' => fake()->randomElement(['home_top', 'home_sidebar', 'category_top', 'product_sidebar', 'search_results']),
         'status' => fake()->randomElement(['pending', 'active', 'paused', 'completed']),
         'start_date' => $startDate,
         'end_date' => $endDate,
         'targeting' => [
            'categories' => [],
            'locations' => [],
            'devices' => ['desktop', 'mobile'],
         ],
         'budget' => fake()->randomFloat(2, 100, 1000),
         'price' => fake()->randomFloat(2, 50, 500),
         'spent' => fake()->randomFloat(2, 0, 100),
         'clicks' => fake()->numberBetween(0, 1000),
         'impressions' => fake()->numberBetween(1000, 10000),
         'ctr' => fake()->randomFloat(2, 0, 5),
      ];
   }

   public function active(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'active',
         'start_date' => now()->subDays(rand(1, 10)),
         'end_date' => now()->addDays(rand(10, 30)),
      ]);
   }

   public function pending(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'pending',
      ]);
   }

   public function paused(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'paused',
      ]);
   }

   public function completed(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'completed',
      ]);
   }
}
