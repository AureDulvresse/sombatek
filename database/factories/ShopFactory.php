<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopFactory extends Factory
{
   protected $model = Shop::class;

   public function definition(): array
   {
      $name = fake()->company();
      $slug = Str::slug($name);
      $isVerified = fake()->boolean(70);
      $status = fake()->randomElement(['active', 'pending', 'suspended']);
      $commissionRate = fake()->randomFloat(2, 5, 15);

      return [
         'user_id' => User::factory(),
         'name' => $name,
         'slug' => $slug,
         'description' => fake()->paragraphs(2, true),
         'logo' => 'shops/' . fake()->image('public/storage/shops', 200, 200, 'business', false),
         'cover_image' => 'shops/' . fake()->image('public/storage/shops', 1200, 400, 'business', false),
         'email' => fake()->companyEmail(),
         'phone' => fake()->phoneNumber(),
         'address' => fake()->address(),
         'city' => fake()->city(),
         'state' => fake()->state(),
         'country' => fake()->country(),
         'postal_code' => fake()->postcode(),
         'website' => fake()->url(),
         'social_media' => json_encode([
            'facebook' => fake()->url(),
            'twitter' => fake()->url(),
            'instagram' => fake()->url(),
            'linkedin' => fake()->url()
         ]),
         'commission_rate' => $commissionRate,
         'minimum_order_amount' => fake()->randomFloat(2, 10, 50),
         'delivery_radius' => fake()->numberBetween(5, 50),
         'is_verified' => $isVerified,
         'status' => $status,
         'rating' => fake()->randomFloat(1, 1, 5),
         'review_count' => fake()->numberBetween(0, 100),
         'total_sales' => fake()->numberBetween(0, 1000),
         'total_orders' => fake()->numberBetween(0, 500),
         'total_products' => fake()->numberBetween(0, 200),
         'meta_title' => $name,
         'meta_description' => fake()->sentence(),
         'meta_keywords' => implode(', ', fake()->words(5))
      ];
   }

   public function verified(): static
   {
      return $this->state(fn(array $attributes) => [
         'is_verified' => true
      ]);
   }

   public function active(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'active'
      ]);
   }

   public function suspended(): static
   {
      return $this->state(fn(array $attributes) => [
         'status' => 'suspended'
      ]);
   }
}
