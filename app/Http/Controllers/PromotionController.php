<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
   public function index()
   {
      $promotions = Promotion::latest()->paginate(10);
      return Inertia::render('Promotions/Index', compact('promotions'));
   }

   public function create()
   {
      return Inertia::render('Promotions/Create');
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'code' => 'required|unique:promotions|string|max:255',
         'description' => 'nullable|string',
         'discount_type' => 'required|in:percentage,fixed',
         'discount_value' => 'required|numeric|min:0',
         'is_active' => 'boolean',
         'expires_at' => 'nullable|date|after:now',
         'min_order_amount' => 'required|numeric|min:0',
         'max_discount' => 'nullable|numeric|min:0',
         'usage_limit' => 'nullable|integer|min:1',
      ]);

      Promotion::create($validated);

      return redirect()->route('promotions.index')
         ->with('success', 'Promotion créée avec succès.');
   }

   public function edit(Promotion $promotion)
   {
      return Inertia::render('Promotions/Edit', compact('promotion'));
   }

   public function update(Request $request, Promotion $promotion)
   {
      $validated = $request->validate([
         'code' => 'required|string|max:255|unique:promotions,code,' . $promotion->id,
         'description' => 'nullable|string',
         'discount_type' => 'required|in:percentage,fixed',
         'discount_value' => 'required|numeric|min:0',
         'is_active' => 'boolean',
         'expires_at' => 'nullable|date|after:now',
         'min_order_amount' => 'required|numeric|min:0',
         'max_discount' => 'nullable|numeric|min:0',
         'usage_limit' => 'nullable|integer|min:1',
      ]);

      $promotion->update($validated);

      return redirect()->route('promotions.index')
         ->with('success', 'Promotion mise à jour avec succès.');
   }

   public function destroy(Promotion $promotion)
   {
      $promotion->delete();
      return redirect()->route('promotions.index')
         ->with('success', 'Promotion supprimée avec succès.');
   }

   public function toggleStatus(Promotion $promotion)
   {
      $promotion->update(['is_active' => !$promotion->is_active]);
      return back()->with('success', 'Statut de la promotion mis à jour.');
   }

   public function validateCode(Request $request)
   {
      $promotion = Promotion::where('code', $request->code)
         ->where('is_active', true)
         ->where(function ($query) {
            $query->whereNull('expires_at')
               ->orWhere('expires_at', '>', now());
         })
         ->where(function ($query) {
            $query->whereNull('usage_limit')
               ->orWhere('used_count', '<', DB::raw('usage_limit'));
         })
         ->first();

      if (!$promotion) {
         return response()->json(['valid' => false]);
      }

      return response()->json([
         'valid' => true,
         'promotion' => $promotion
      ]);
   }
}
