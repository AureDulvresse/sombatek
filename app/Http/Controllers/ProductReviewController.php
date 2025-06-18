<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
   /**
    * Store a newly created review in storage.
    */
   public function store(Request $request)
   {
      $validated = $request->validate([
         'product_id' => 'required|exists:products,id',
         'rating' => 'required|integer|min:1|max:5',
         'comment' => 'required|string|min:10'
      ]);

      $review = ProductReview::create([
         'customer_id' => Auth::id(),
         'product_id' => $validated['product_id'],
         'rating' => $validated['rating'],
         'comment' => $validated['comment']
      ]);

      return response()->json([
         'message' => 'Avis créé avec succès',
         'review' => $review
      ], 201);
   }

   /**
    * Update the specified review in storage.
    */
   public function update(Request $request, ProductReview $review)
   {
      $this->authorize('update', $review);

      $validated = $request->validate([
         'rating' => 'required|integer|min:1|max:5',
         'comment' => 'required|string|min:10'
      ]);

      $review->update($validated);

      return response()->json([
         'message' => 'Avis mis à jour avec succès',
         'review' => $review
      ]);
   }

   /**
    * Remove the specified review from storage.
    */
   public function destroy(ProductReview $review)
   {
      $this->authorize('delete', $review);

      $review->delete();

      return response()->json([
         'message' => 'Avis supprimé avec succès'
      ]);
   }
}
