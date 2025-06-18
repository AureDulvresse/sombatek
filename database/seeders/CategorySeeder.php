<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le dossier pour les images si nécessaire
        if (!Storage::exists('public/categories')) {
            Storage::makeDirectory('public/categories');
        }

        $categories = [
            [
                'name' => 'Électronique',
                'description' => 'Tous les produits électroniques et gadgets',
                'is_active' => true,
                'parent_id' => null,
                'order' => 1
            ],
            [
                'name' => 'Mode',
                'description' => 'Vêtements et accessoires de mode',
                'is_active' => true,
                'parent_id' => null,
                'order' => 2
            ],
            [
                'name' => 'Maison & Jardin',
                'description' => 'Décoration et ameublement',
                'is_active' => true,
                'parent_id' => null,
                'order' => 3
            ],
            [
                'name' => 'Sports & Loisirs',
                'description' => 'Équipements sportifs et activités de loisir',
                'is_active' => true,
                'parent_id' => null,
                'order' => 4
            ],
            [
                'name' => 'Beauté & Santé',
                'description' => 'Produits de beauté et de santé',
                'is_active' => true,
                'parent_id' => null,
                'order' => 5
            ]
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);

            // Créer la catégorie
            $newCategory = Category::create([
                'name' => $category['name'],
                'slug' => $slug,
                'description' => $category['description'],
                'is_active' => $category['is_active'],
                'parent_id' => $category['parent_id'],
                'order' => $category['order'],
                'image' => 'categories/default.jpg' // Image par défaut
            ]);

            // Générer une image aléatoire
            try {
                $imageUrl = "https://picsum.photos/800/600";
                $imageContent = file_get_contents($imageUrl);
                $imagePath = "categories/{$slug}.jpg";

                Storage::put("public/{$imagePath}", $imageContent);
                $newCategory->update(['image' => $imagePath]);
            } catch (\Exception $e) {
                // En cas d'erreur, on garde l'image par défaut
                Log::error("Erreur lors de la génération de l'image pour la catégorie {$category['name']}: " . $e->getMessage());
            }
        }

        // Créer des sous-catégories
        $subcategories = [
            [
                'name' => 'Smartphones',
                'description' => 'Téléphones mobiles et accessoires',
                'parent_id' => 1,
                'order' => 1
            ],
            [
                'name' => 'Ordinateurs',
                'description' => 'Ordinateurs portables et de bureau',
                'parent_id' => 1,
                'order' => 2
            ],
            [
                'name' => 'Vêtements Homme',
                'description' => 'Mode masculine',
                'parent_id' => 2,
                'order' => 1
            ],
            [
                'name' => 'Vêtements Femme',
                'description' => 'Mode féminine',
                'parent_id' => 2,
                'order' => 2
            ]
        ];

        foreach ($subcategories as $subcategory) {
            $slug = Str::slug($subcategory['name']);

            // Créer la sous-catégorie
            $newSubcategory = Category::create([
                'name' => $subcategory['name'],
                'slug' => $slug,
                'description' => $subcategory['description'],
                'is_active' => true,
                'parent_id' => $subcategory['parent_id'],
                'order' => $subcategory['order'],
                'image' => 'categories/default.jpg'
            ]);

            // Générer une image aléatoire
            try {
                $imageUrl = "https://picsum.photos/800/600";
                $imageContent = file_get_contents($imageUrl);
                $imagePath = "categories/{$slug}.jpg";

                Storage::put("public/{$imagePath}", $imageContent);
                $newSubcategory->update(['image' => $imagePath]);
            } catch (\Exception $e) {
                Log::error("Erreur lors de la génération de l'image pour la sous-catégorie {$subcategory['name']}: " . $e->getMessage());
            }
        }
    }
}
