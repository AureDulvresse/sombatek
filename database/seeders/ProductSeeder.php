<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer que nous avons des catégories et des boutiques
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }
        if (Shop::count() === 0) {
            $this->call(ShopSeeder::class);
        }

        // Récupérer toutes les catégories et boutiques
        $categories = Category::all();
        $shops = Shop::where('status', 'active')->get();

        // Créer des produits pour chaque boutique
        foreach ($shops as $shop) {
            // Créer des produits normaux
            Product::factory()
                ->count(rand(10, 20))
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                ]);

            // Créer des produits en vedette
            Product::factory()
                ->count(rand(3, 5))
                ->featured()
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                ]);

            // Créer des produits en solde
            Product::factory()
                ->count(rand(5, 10))
                ->onSale()
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                ]);

            // Créer quelques produits en rupture de stock
            Product::factory()
                ->count(rand(2, 4))
                ->outOfStock()
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                ]);

            // Créer des produits avec stock faible
            Product::factory()
                ->count(rand(3, 6))
                ->lowStock()
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                ]);
        }

        // Créer des produits premium avec des variations
        $premiumShops = $shops->take(3);
        foreach ($premiumShops as $shop) {
            Product::factory()
                ->count(rand(5, 8))
                ->featured()
                ->withVariations()
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                    'price' => rand(1000, 5000),
                ]);
        }
    }
}
