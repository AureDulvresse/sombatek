<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
   protected $model = CartItem::class;

   public function definition(): array
   {
      $quantity = fake()->numberBetween(1, 5);
      $product = Product::factory()->create();
      $price = $product->sale_price ?? $product->price;
      $subtotal = $price * $quantity;

      return [
         'cart_id' => Cart::factory(),
         'product_id' => $product->id,
         'product_name' => $product->name,
         'product_sku' => $product->sku,
         'quantity' => $quantity,
         'price' => $price,
         'subtotal' => $subtotal,
         'options' => json_encode([
            'color' => fake()->optional()->randomElement(['red', 'blue', 'green', 'black', 'white']),
            'size' => fake()->optional()->randomElement(['S', 'M', 'L', 'XL']),
            'material' => fake()->optional()->randomElement(['cotton', 'polyester', 'wool', 'silk'])
         ])
      ];
   }

   public function withQuantity(int $quantity): static
   {
      return $this->state(function (array $attributes) use ($quantity) {
         $price = $attributes['price'];
         return [
            'quantity' => $quantity,
            'subtotal' => $price * $quantity
         ];
      });
   }

   public function withOptions(array $options): static
   {
      return $this->state(fn(array $attributes) => [
         'options' => json_encode($options)
      ]);
   }
}
