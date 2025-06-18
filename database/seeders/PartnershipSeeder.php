<?php

namespace Database\Seeders;

use App\Models\Partnership;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer que nous avons des boutiques
        if (Shop::count() === 0) {
            $this->call(ShopSeeder::class);
        }

        $shops = Shop::where('status', 'active')->get();

        // Vérifier qu'il y a au moins 2 boutiques pour créer des partenariats
        if ($shops->count() < 2) {
            return;
        }

        // Créer des partenariats entre les boutiques
        foreach ($shops as $shop) {
            // Sélectionner aléatoirement 1-3 autres boutiques pour le partenariat
            $potentialPartners = $shops->where('id', '!=', $shop->id);

            if ($potentialPartners->isEmpty()) {
                continue;
            }

            $partnerCount = min(rand(1, 3), $potentialPartners->count());
            $partners = $potentialPartners->random($partnerCount);

            foreach ($partners as $partner) {
                // Vérifier si le partenariat existe déjà
                $existingPartnership = Partnership::where(function ($query) use ($shop, $partner) {
                    $query->where('shop_id', $shop->id)
                        ->where('promoter_id', $partner->id);
                })->orWhere(function ($query) use ($shop, $partner) {
                    $query->where('shop_id', $partner->id)
                        ->where('promoter_id', $shop->id);
                })->exists();

                if (!$existingPartnership) {
                    Partnership::create([
                        'shop_id' => $shop->id,
                        'promoter_id' => $partner->id,
                        'code' => 'PART-' . strtoupper(uniqid()),
                        'status' => 'active',
                        'commission_rate' => fake()->randomFloat(2, 5, 20),
                        'start_date' => now(),
                        'end_date' => now()->addMonths(rand(3, 12)),
                        'terms' => json_encode([
                            'minimum_sales' => fake()->numberBetween(1000, 10000),
                            'payment_terms' => fake()->randomElement(['monthly', 'quarterly', 'annually']),
                        ]),
                    ]);
                }
            }
        }
    }
}
