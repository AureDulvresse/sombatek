<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
   public function run(): void
   {
      // Récupérer tous les produits
      $products = Product::all();
      $customers = User::where('role', 'customer')->get();

      if ($customers->isEmpty()) {
         return;
      }

      foreach ($products as $product) {
         // Créer 2 à 5 avis par produit
         $reviewCount = rand(2, 5);

         // 70% d'avis positifs, 30% d'avis négatifs
         $positiveCount = (int)($reviewCount * 0.7);
         $negativeCount = $reviewCount - $positiveCount;

         // Créer les avis positifs
         ProductReview::factory()
            ->count($positiveCount)
            ->positive()
            ->create([
               'product_id' => $product->id,
               'customer_id' => $customers->random()->id
            ]);

         // Créer les avis négatifs
         ProductReview::factory()
            ->count($negativeCount)
            ->negative()
            ->create([
               'product_id' => $product->id,
               'customer_id' => $customers->random()->id
            ]);

         // Mettre à jour la note moyenne et le nombre d'avis du produit
         $product->update([
            'average_rating' => $product->productReviews()->avg('rating'),
            'reviews_count' => $product->productReviews()->count(),
         ]);
      }
   }
}
