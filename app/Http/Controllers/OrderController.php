<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
   public function index()
   {
      $orders = Auth::user()->orders()
         ->with(['items.product', 'shop'])
         ->latest()
         ->paginate(10);

      return Inertia::render('Orders/Index', [
         'orders' => $orders
      ]);
   }

   public function show(Order $order)
   {
      $this->authorize('view', $order);

      $order->load(['items.product', 'shop', 'address']);

      return Inertia::render('Orders/Show', [
         'order' => $order
      ]);
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'address_id' => 'required|exists:addresses,id',
         'payment_method' => 'required|string',
         'items' => 'required|array',
         'items.*.product_id' => 'required|exists:products,id',
         'items.*.quantity' => 'required|integer|min:1'
      ]);

      $order = Auth::user()->orders()->create([
         'address_id' => $validated['address_id'],
         'payment_method' => $validated['payment_method'],
         'status' => 'pending'
      ]);

      foreach ($validated['items'] as $item) {
         $order->items()->create([
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
         ]);
      }

      // Vider le panier
      Auth::user()->cart()->delete();

      return redirect()->route('orders.confirmation', $order)
         ->with('success', 'Commande créée avec succès.');
   }

   public function confirmation(Order $order)
   {
      $this->authorize('view', $order);

      return Inertia::render('Orders/Confirmation', [
         'order' => $order
      ]);
   }

   public function cancel(Order $order)
   {
      $this->authorize('update', $order);

      if ($order->status === 'pending') {
         $order->update(['status' => 'cancelled']);
         return redirect()->route('orders.show', $order)
            ->with('success', 'Commande annulée avec succès.');
      }

      return back()->with('error', 'Cette commande ne peut pas être annulée.');
   }

   public function shopIndex()
   {
      $shop = Auth::user()->shop;
      if (!$shop) {
         abort(403, 'Aucune boutique associée à ce compte.');
      }
      $orders = Order::where('shop_id', $shop->id)
         ->with(['items.product', 'customer', 'address'])
         ->latest()
         ->paginate(10);
      return Inertia::render('Shop/Orders', [
         'orders' => $orders
      ]);
   }

   public function shopShow(Order $order)
   {
      $shop = Auth::user()->shop;
      if (!$shop || $order->shop_id !== $shop->id) {
         abort(403, 'Accès non autorisé à cette commande.');
      }
      $order->load(['items.product', 'customer', 'address']);
      return Inertia::render('Shop/OrderDetail', [
         'order' => $order
      ]);
   }

   public function updateStatus(Request $request, Order $order)
   {
      $shop = Auth::user()->shop;
      if (!$shop || $order->shop_id !== $shop->id) {
         abort(403, 'Accès non autorisé à cette commande.');
      }
      $validated = $request->validate([
         'status' => 'required|string|in:en attente,en cours,livrée,annulée'
      ]);
      $order->update(['status' => $validated['status']]);
      // (Optionnel) Ajouter une notification au client ici
      return back()->with('success', 'Statut de la commande mis à jour.');
   }
}
