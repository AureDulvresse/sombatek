<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Création du Super Admin Root
        User::updateOrCreate(
            ['email' => 'adentrepreneur02@gmail.com'],
            [
                'name' => 'Admin Root',
                'password' => Hash::make('Rootp@ssw0rd'),
                'role' => 'admin',
                'root' => true,
                'is_active' => true,
                'remember_token' => Str::random(10),
            ]
        );

        // ✅ Création de l’Admin gestionnaire (non-root)
        User::updateOrCreate(
            ['email' => 'manager@admin.com'],
            [
                'name' => 'Gestionnaire Admin',
                'password' => Hash::make('P@ssw0rd'),
                'role' => 'admin',
                'root' => false,
                'is_active' => true,
                'remember_token' => Str::random(10),
            ]
        );
    }
}
