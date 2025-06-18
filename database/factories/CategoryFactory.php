<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
   protected $model = Category::class;

   public function definition(): array
   {
      $name = fake()->unique()->words(rand(1, 3), true);
      $slug = Str::slug($name);
      $isActive = fake()->boolean(90);
      $isFeatured = fake()->boolean(20);
      $order = fake()->numberBetween(1, 100);

      return [
         'name' => $name,
         'slug' => $slug,
         'description' => fake()->paragraph(),
         'image' => 'categories/' . fake()->image('public/storage/categories', 800, 600, 'category', false),
         'parent_id' => null,
         'is_active' => $isActive,
         'is_featured' => $isFeatured,
         'order' => $order,
         'meta_title' => $name,
         'meta_description' => fake()->sentence(),
         'meta_keywords' => implode(', ', fake()->words(5)),
         'icon' => fake()->randomElement(['shopping-bag', 'tshirt', 'laptop', 'mobile', 'home', 'utensils', 'heart', 'star']),
         'color' => fake()->hexColor(),
         'products_count' => 0
      ];
   }

   public function active(): static
   {
      return $this->state(fn(array $attributes) => [
         'is_active' => true
      ]);
   }

   public function featured(): static
   {
      return $this->state(fn(array $attributes) => [
         'is_featured' => true
      ]);
   }

   public function withParent(Category $parent): static
   {
      return $this->state(fn(array $attributes) => [
         'parent_id' => $parent->id
      ]);
   }
}
