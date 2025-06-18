<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les boutiques actives
        $shops = Shop::where('status', 'active')->get();

        // Plans d'abonnement
        $plans = [
            [
                'name' => 'Basic',
                'type' => 'monthly',
                'amount' => 29.99,
                'features' => [
                    'products' => 100,
                    'storage' => '5GB',
                    'support' => 'email',
                    'analytics' => 'basic'
                ]
            ],
            [
                'name' => 'Pro',
                'type' => 'monthly',
                'amount' => 79.99,
                'features' => [
                    'products' => 500,
                    'storage' => '20GB',
                    'support' => 'priority',
                    'analytics' => 'advanced'
                ]
            ],
            [
                'name' => 'Enterprise',
                'type' => 'monthly',
                'amount' => 199.99,
                'features' => [
                    'products' => 'unlimited',
                    'storage' => '100GB',
                    'support' => '24/7',
                    'analytics' => 'premium'
                ]
            ]
        ];

        // Créer des abonnements pour chaque boutique
        foreach ($shops as $shop) {
            $plan = $plans[array_rand($plans)];

            $subscription = Subscription::factory()->create([
                'user_id' => $shop->user_id,
                'name' => $plan['name'],
                'type' => $plan['type'],
                'amount' => $plan['amount'],
                'features' => json_encode($plan['features']),
                'stripe_id' => 'sub_' . strtoupper(uniqid()),
                'stripe_status' => 'active',
                'stripe_price' => 'price_' . strtoupper(uniqid()),
                'quantity' => 1,
                'trial_ends_at' => now()->addDays(14),
                'ends_at' => null
            ]);

            // Créer les items d'abonnement
            $subscription->items()->create([
                'stripe_id' => 'si_' . strtoupper(uniqid()),
                'stripe_product' => 'prod_' . strtoupper(uniqid()),
                'stripe_price' => $subscription->stripe_price,
                'quantity' => 1
            ]);
        }
    }
}
