<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductReviewFactory extends Factory
{
   protected $model = ProductReview::class;

   public function definition(): array
   {
      return [
         'customer_id' => User::factory(),
         'product_id' => Product::factory(),
         'rating' => fake()->numberBetween(1, 5),
         'comment' => fake()->paragraphs(rand(1, 3), true),
      ];
   }

   public function positive(): static
   {
      return $this->state(fn(array $attributes) => [
         'rating' => fake()->numberBetween(4, 5),
      ]);
   }

   public function negative(): static
   {
      return $this->state(fn(array $attributes) => [
         'rating' => fake()->numberBetween(1, 2),
      ]);
   }
}
