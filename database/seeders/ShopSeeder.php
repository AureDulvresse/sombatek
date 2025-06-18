<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'shop')->get();

        foreach ($users as $user) {
            $name = fake()->company();
            $slug = Str::slug($name);

            $baseSlug = $slug;
            $counter = 1;
            while (Shop::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            Shop::create([
                'user_id' => $user->id,
                'name' => $name,
                'slug' => $slug,
                'logo' => fake()->imageUrl(200, 200, 'business'),
                'description' => fake()->paragraph(),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'email' => fake()->companyEmail(),
                'website' => fake()->url(),
                'social_media' => json_encode([
                    'facebook' => fake()->url(),
                    'twitter' => fake()->url(),
                    'instagram' => fake()->url(),
                ]),
                'status' => 'active',
                'is_verified' => true,
                'commission_rate' => fake()->randomFloat(2, 5, 15),
                'settings' => json_encode([
                    'theme' => 'light',
                    'currency' => 'EUR',
                    'language' => 'fr',
                ]),
                'opening_hours' => json_encode([
                    'monday' => ['09:00-12:00', '14:00-18:00'],
                    'tuesday' => ['09:00-12:00', '14:00-18:00'],
                    'wednesday' => ['09:00-12:00', '14:00-18:00'],
                    'thursday' => ['09:00-12:00', '14:00-18:00'],
                    'friday' => ['09:00-12:00', '14:00-18:00'],
                    'saturday' => ['10:00-16:00'],
                    'sunday' => [],
                ]),
                'delivery_options' => json_encode([
                    'standard' => [
                        'enabled' => true,
                        'price' => 5,
                        'min_order' => 20,
                    ],
                    'express' => [
                        'enabled' => true,
                        'price' => 10,
                        'min_order' => 30,
                    ],
                ]),
                'payment_methods' => json_encode([
                    'card' => true,
                    'cash' => true,
                    'paypal' => true,
                ]),
                'minimum_order_amount' => fake()->randomFloat(2, 10, 30),
                'delivery_radius' => fake()->numberBetween(5, 20),
            ]);
        }
    }
}
