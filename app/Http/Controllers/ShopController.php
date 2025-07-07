<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Notifications\ShopApproved;
use App\Notifications\ShopRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ShopController extends Controller
{
   public function index()
   {
      $shops = Shop::with(['user', 'products'])
         ->where('is_verified', true)
         ->where('status', 'active')
         ->latest()
         ->paginate(12);

      return Inertia::render('Shops/Index', [
         'shops' => $shops
      ]);
   }

   public function create()
   {
      return Inertia::render('Shops/Create');
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'required|string',
         'address' => 'required|string|max:255',
         'phone' => 'required|string|max:20',
         'email' => 'required|email|max:255',
         'website' => 'nullable|url|max:255',
         'social_media' => 'nullable|array',
         'opening_hours' => 'required|array',
         'delivery_options' => 'required|array',
         'payment_methods' => 'required|array',
         'minimum_order_amount' => 'required|numeric|min:0',
         'delivery_radius' => 'required|integer|min:0',
      ]);

      $shop = Shop::create([
         'user_id' => Auth::user()->id(),
         'name' => $validated['name'],
         'slug' => Str::slug($validated['name']),
         'description' => $validated['description'],
         'address' => $validated['address'],
         'phone' => $validated['phone'],
         'email' => $validated['email'],
         'website' => $validated['website'],
         'social_media' => $validated['social_media'],
         'opening_hours' => $validated['opening_hours'],
         'delivery_options' => $validated['delivery_options'],
         'payment_methods' => $validated['payment_methods'],
         'minimum_order_amount' => $validated['minimum_order_amount'],
         'delivery_radius' => $validated['delivery_radius'],
         'status' => 'pending',
         'is_verified' => false,
      ]);

      return redirect()->route('shops.show', $shop)
         ->with('success', 'Votre boutique a été créée et est en attente de validation.');
   }

   public function show(Shop $shop)
   {
      $shop->load(['user', 'products' => function ($query) {
         $query->where('is_active', true);
      }]);

      // Récupère les catégories des produits de la boutique
      $categories = $shop->products->pluck('category')->unique('id')->values();

      // Récupère les avis (à adapter selon ta structure)
      $reviews = $shop->reviews ?? [];

      return Inertia::render('Shops/Show', [
         'shop' => $shop,
         'products' => $shop->products,
         'categories' => $categories,
         'reviews' => $reviews,
      ]);
   }

   public function edit(Shop $shop)
   {
      $this->authorize('update', $shop);

      return Inertia::render('Shops/Edit', [
         'shop' => $shop
      ]);
   }

   public function update(Request $request, Shop $shop)
   {
      $this->authorize('update', $shop);

      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'required|string',
         'address' => 'required|string|max:255',
         'phone' => 'required|string|max:20',
         'email' => 'required|email|max:255',
         'website' => 'nullable|url|max:255',
         'social_media' => 'nullable|array',
         'opening_hours' => 'required|array',
         'delivery_options' => 'required|array',
         'payment_methods' => 'required|array',
         'minimum_order_amount' => 'required|numeric|min:0',
         'delivery_radius' => 'required|integer|min:0',
      ]);

      $shop->update($validated);

      return redirect()->route('shops.show', $shop)
         ->with('success', 'Boutique mise à jour avec succès.');
   }

   public function approve(Shop $shop)
   {
      $shop->update([
         'is_verified' => true,
         'status' => 'active'
      ]);

      $shop->user->notify(new ShopApproved($shop));

      return back()->with('success', 'Boutique approuvée avec succès.');
   }

   public function reject(Shop $shop)
   {
      $shop->update([
         'status' => 'rejected'
      ]);

      $shop->user->notify(new ShopRejected($shop));

      return back()->with('success', 'Boutique rejetée avec succès.');
   }

   public function destroy(Shop $shop)
   {
      $shop->delete();

      return redirect()->route('dashboard')
         ->with('success', 'Boutique supprimée avec succès.');
   }

   public function pending()
   {
      $shops = Shop::where('status', 'pending')->with('user')->latest()->paginate(15);
      return Inertia::render('Shops/Pending', [
         'shops' => $shops
      ]);
   }
}
