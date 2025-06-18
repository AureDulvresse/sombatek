<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
   protected $model = ProductImage::class;

   public function definition(): array
   {
      return [
         'product_id' => Product::factory(),
         'path' => fake()->imageUrl(800, 600, 'product'),
         'order' => fake()->numberBetween(0, 10),
         'is_default' => false,
      ];
   }

   public function default(): static
   {
      return $this->state(fn(array $attributes) => [
         'is_default' => true,
      ]);
   }
}
