<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'utilisateur admin s'il n'existe pas
        if (!User::where('email', 'admin@sombatek.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@sombatek.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Créer des utilisateurs avec différents rôles
        $roles = ['customer', 'shop', 'promoter'];
        foreach ($roles as $role) {
            User::factory()
                ->count(5)
                ->create([
                    'role' => $role,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

            // Créer quelques utilisateurs inactifs
            User::factory()
                ->count(2)
                ->create([
                    'role' => $role,
                    'is_active' => false,
                ]);

            // Créer des utilisateurs non vérifiés
            User::factory()
                ->count(3)
                ->create([
                    'role' => $role,
                    'email_verified_at' => null,
                ]);
        }

        // Créer des utilisateurs premium
        User::factory()
            ->count(3)
            ->create([
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'settings' => [
                    'premium' => true,
                    'notifications' => [
                        'email' => true,
                        'sms' => true,
                        'push' => true,
                    ],
                ],
            ]);
    }
}
