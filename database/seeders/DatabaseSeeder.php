<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,         // D'abord l'admin
            // UserSeeder::class,         // D'abord les utilisateurs
            // CategorySeeder::class,     // Ensuite les catégories
            // ShopSeeder::class,         // Puis les boutiques
            // ProductSeeder::class,      // Ensuite les produits
            // OrderSeeder::class,        // Puis les commandes
            // AdvertisementSeeder::class, // Ensuite les publicités
            // PartnershipSeeder::class,   // Puis les partenariats
            // SubscriptionSeeder::class,  // Ensuite les abonnements
            // CommissionSeeder::class,    // Enfin les commissions
            // ProductReviewSeeder::class,  // Ensuite les avis de produit
        ]);
    }
}
