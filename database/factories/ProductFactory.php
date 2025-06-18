<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
   protected $model = Product::class;
   private static $counter = 0;

   public function definition(): array
   {
      $name = fake()->words(3, true);
      $price = fake()->randomFloat(2, 10, 1000);
      $hasSale = fake()->boolean(30);
      $salePrice = $hasSale ? $price * fake()->randomFloat(2, 0.5, 0.9) : null;
      $stock = fake()->numberBetween(0, 100);
      $isActive = fake()->boolean(80);
      $isFeatured = fake()->boolean(20);
      $rating = fake()->randomFloat(1, 1, 5);
      $reviewCount = fake()->numberBetween(0, 100);
      self::$counter++;

      return [
         'shop_id' => Shop::factory(),
         'category_id' => Category::factory(),
         'name' => $name,
         'slug' => Str::slug($name) . '-' . self::$counter,
         'description' => fake()->paragraphs(3, true),
         'price' => $price,
         'sale_price' => $salePrice,
         'stock' => $stock,
         'sku' => 'SKU-' . strtoupper(Str::random(8)),
         'is_featured' => $isFeatured,
         'is_active' => $isActive,
         'attributes' => [
            'color' => fake()->randomElement(['red', 'blue', 'green', 'black', 'white']),
            'size' => fake()->randomElement(['S', 'M', 'L', 'XL']),
            'material' => fake()->randomElement(['cotton', 'polyester', 'wool', 'silk'])
         ],
         'variations' => null,
         'average_rating' => $rating,
         'reviews_count' => $reviewCount,
         'sales_count' => fake()->numberBetween(0, 500),
         'views_count' => fake()->numberBetween(0, 1000),
      ];
   }

   public function configure()
   {
      return $this->afterCreating(function (Product $product) {
         // Créer 3-5 images pour chaque produit
         $imageCount = fake()->numberBetween(3, 5);
         for ($i = 0; $i < $imageCount; $i++) {
            ProductImage::factory()->create([
               'product_id' => $product->id,
               'path' => fake()->imageUrl(800, 600, 'product'),
               'order' => $i,
               'is_default' => $i === 0
            ]);
         }

         // Définir l'image par défaut
         $defaultImage = $product->images()->where('is_default', true)->first();
         if ($defaultImage) {
            $product->update(['default_image' => $defaultImage->path]);
         }
      });
   }

   public function featured(): static
   {
      return $this->state(fn(array $attributes) => [
         'is_featured' => true,
      ]);
   }

   public function onSale(): static
   {
      return $this->state(fn(array $attributes) => [
         'sale_price' => $attributes['price'] * fake()->randomFloat(2, 0.5, 0.9),
      ]);
   }

   public function withVariations(): static
   {
      return $this->state(function (array $attributes) {
         return [
            'variations' => [
               [
                  'name' => 'Couleur',
                  'options' => [
                     ['value' => 'Noir', 'price_adjustment' => 0],
                     ['value' => 'Blanc', 'price_adjustment' => 0],
                     ['value' => 'Or', 'price_adjustment' => 50],
                  ],
               ],
               [
                  'name' => 'Taille',
                  'options' => [
                     ['value' => 'S', 'price_adjustment' => 0],
                     ['value' => 'M', 'price_adjustment' => 0],
                     ['value' => 'L', 'price_adjustment' => 10],
                     ['value' => 'XL', 'price_adjustment' => 20],
                  ],
               ],
            ],
         ];
      });
   }

   public function outOfStock(): static
   {
      return $this->state(fn(array $attributes) => [
         'stock' => 0,
         'is_active' => false,
      ]);
   }

   public function lowStock(): self
   {
      return $this->state(function (array $attributes) {
         return [
            'stock' => fake()->numberBetween(1, 5),
         ];
      });
   }

   public function inactive(): self
   {
      return $this->state(function (array $attributes) {
         return [
            'is_active' => false,
         ];
      });
   }
}
