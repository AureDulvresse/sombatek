<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function store(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000'
        ]);

        $review = $shop->reviews()->create([
            'customer_id' => Auth::user()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment']
        ]);

        return back()->with('success', 'Avis publié avec succès.');
    }

    public function update(Request $request, Shop $shop, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000'
        ]);

        $review->update($validated);

        return back()->with('success', 'Avis mis à jour avec succès.');
    }

    public function destroy(Shop $shop, Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Avis supprimé avec succès.');
    }
} 