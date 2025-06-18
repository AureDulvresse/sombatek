<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
   public function index()
   {
      $categories = Category::withCount('products')->get();
      return Inertia::render('Categories/Index', [
         'categories' => $categories
      ]);
   }

   public function create()
   {
      return Inertia::render('Categories/Create');
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'nullable|string',
         'image' => 'nullable|image|max:2048'
      ]);

      Category::create($validated);

      return redirect()->route('categories.index')
         ->with('success', 'Catégorie créée avec succès.');
   }

   public function edit(Category $category)
   {
      return Inertia::render('Categories/Edit', [
         'category' => $category
      ]);
   }

   public function update(Request $request, Category $category)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'nullable|string',
         'image' => 'nullable|image|max:2048'
      ]);

      $category->update($validated);

      return redirect()->route('categories.index')
         ->with('success', 'Catégorie mise à jour avec succès.');
   }

   public function destroy(Category $category)
   {
      $category->delete();

      return redirect()->route('categories.index')
         ->with('success', 'Catégorie supprimée avec succès.');
   }

   public function listProduct(Category $category)
   {
      $cat = Category::with(['products'])->find($category);
      return Inertia::render('Category.show', [
         'category' => $cat,
      ]);
   }
}
