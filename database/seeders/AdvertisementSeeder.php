<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si des publicités existent déjà
        if (Advertisement::count() > 0) {
            return;
        }

        // S'assurer que nous avons des boutiques
        if (Shop::count() === 0) {
            $this->call(ShopSeeder::class);
        }

        // S'assurer que nous avons des catégories
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        $shops = Shop::where('status', 'active')->get();
        $categories = Category::all();

        // Créer des publicités pour chaque boutique
        foreach ($shops as $shop) {
            // Créer des publicités actives
            Advertisement::factory()
                ->count(rand(2, 4))
                ->active()
                ->create([
                    'shop_id' => $shop->id,
                    'position' => fake()->randomElement(['home_top', 'home_sidebar', 'category_top', 'product_sidebar', 'search_results']),
                    'targeting' => json_encode([
                        'categories' => $categories->random(rand(1, 3))->pluck('id')->toArray(),
                        'locations' => ['Paris', 'Lyon', 'Marseille'],
                        'devices' => ['desktop', 'mobile'],
                    ]),
                    'budget' => fake()->randomFloat(2, 100, 1000),
                    'price' => fake()->randomFloat(2, 50, 500),
                    'spent' => fake()->randomFloat(2, 0, 100),
                    'ctr' => fake()->randomFloat(2, 0, 5),
                ]);

            // Créer des publicités en attente
            Advertisement::factory()
                ->count(rand(1, 3))
                ->pending()
                ->create([
                    'shop_id' => $shop->id,
                    'position' => fake()->randomElement(['home_top', 'home_sidebar', 'category_top', 'product_sidebar', 'search_results']),
                    'budget' => fake()->randomFloat(2, 100, 1000),
                    'price' => fake()->randomFloat(2, 50, 500),
                    'targeting' => json_encode([]),
                ]);

            // Créer des publicités en pause
            Advertisement::factory()
                ->count(rand(1, 2))
                ->paused()
                ->create([
                    'shop_id' => $shop->id,
                    'position' => fake()->randomElement(['home_top', 'home_sidebar', 'category_top', 'product_sidebar', 'search_results']),
                    'budget' => fake()->randomFloat(2, 100, 1000),
                    'price' => fake()->randomFloat(2, 50, 500),
                    'spent' => fake()->randomFloat(2, 0, 100),
                    'ctr' => fake()->randomFloat(2, 0, 5),
                    'targeting' => json_encode([]),
                ]);

            // Créer des publicités terminées
            Advertisement::factory()
                ->count(rand(1, 2))
                ->completed()
                ->create([
                    'shop_id' => $shop->id,
                    'position' => fake()->randomElement(['home_top', 'home_sidebar', 'category_top', 'product_sidebar', 'search_results']),
                    'budget' => fake()->randomFloat(2, 100, 1000),
                    'price' => fake()->randomFloat(2, 50, 500),
                    'spent' => fake()->randomFloat(2, 0, 100),
                    'ctr' => fake()->randomFloat(2, 0, 5),
                    'targeting' => json_encode([]),
                ]);
        }
    }
}
