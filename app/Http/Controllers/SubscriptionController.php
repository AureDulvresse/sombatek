<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
   public function index()
   {
      $subscriptions = Auth::user()->subscriptions()
         ->with(['items.product'])
         ->latest()
         ->paginate(10);

      return Inertia::render('Subscriptions/Index', [
         'subscriptions' => $subscriptions
      ]);
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'product_id' => 'required|exists:products,id',
         'quantity' => 'required|integer|min:1',
         'frequency' => 'required|string|in:weekly,monthly,quarterly',
         'start_date' => 'required|date|after:today',
         'address_id' => 'required|exists:addresses,id'
      ]);

      $subscription = Auth::user()->subscriptions()->create([
         'status' => 'active',
         'frequency' => $validated['frequency'],
         'start_date' => $validated['start_date'],
         'address_id' => $validated['address_id']
      ]);

      $subscription->items()->create([
         'product_id' => $validated['product_id'],
         'quantity' => $validated['quantity']
      ]);

      return redirect()->route('subscriptions.index')
         ->with('success', 'Abonnement créé avec succès.');
   }

   public function update(Request $request, Subscription $subscription)
   {
      $this->authorize('update', $subscription);

      $validated = $request->validate([
         'status' => 'required|string|in:active,paused,cancelled',
         'frequency' => 'required|string|in:weekly,monthly,quarterly',
         'address_id' => 'required|exists:addresses,id'
      ]);

      $subscription->update($validated);

      return redirect()->route('subscriptions.index')
         ->with('success', 'Abonnement mis à jour avec succès.');
   }

   public function destroy(Subscription $subscription)
   {
      $this->authorize('delete', $subscription);

      $subscription->update(['status' => 'cancelled']);

      return redirect()->route('subscriptions.index')
         ->with('success', 'Abonnement annulé avec succès.');
   }
}
