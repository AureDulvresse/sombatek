<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\CardException;

class PaymentController extends Controller
{
   public function checkout(Order $order)
   {
      if ($order->status !== 'pending') {
         return redirect()->route('orders.show', $order)
            ->with('error', 'Cette commande ne peut plus être payée.');
      }

      return Inertia::render('Payments/Checkout', [
         'order' => $order->load(['items.product', 'items.shop']),
         'intent' => Auth::user()->createSetupIntent(),
      ]);
   }

   public function process(Request $request, Order $order)
   {
      try {
         $user = Auth::user();
         $paymentMethod = $request->input('payment_method');

         // Vérifier si l'utilisateur a déjà une méthode de paiement
         if (!$user->hasDefaultPaymentMethod()) {
            $user->updateDefaultPaymentMethod($paymentMethod);
         }

         // Créer l'intention de paiement
         $payment = $user->charge($order->total * 100, $paymentMethod, [
            'description' => "Commande #{$order->id}",
            'metadata' => [
               'order_id' => $order->id,
            ],
         ]);

         // Mettre à jour le statut de la commande
         $order->update([
            'status' => 'paid',
            'payment_id' => $payment->id,
            'paid_at' => now(),
         ]);

         return redirect()->route('orders.show', $order)
            ->with('success', 'Paiement effectué avec succès !');
      } catch (CardException $e) {
         return back()->with('error', 'Erreur de carte : ' . $e->getMessage());
      } catch (IncompletePayment $e) {
         return redirect()->route('payment.verify', [
            'order' => $order->id,
            'payment_intent' => $e->payment->id,
         ]);
      } catch (\Exception $e) {
         return back()->with('error', 'Une erreur est survenue lors du paiement.');
      }
   }

   public function verify(Request $request, Order $order)
   {
      $paymentIntent = $request->input('payment_intent');

      return Inertia::render('Payments/Verify', [
         'order' => $order,
         'paymentIntent' => $paymentIntent,
      ]);
   }

   public function confirm(Request $request, Order $order)
   {
      try {
         $user = Auth::user();
         $paymentIntent = $request->input('payment_intent');

         // Confirmer le paiement
         $payment = $user->confirmPayment($paymentIntent);

         // Mettre à jour le statut de la commande
         $order->update([
            'status' => 'paid',
            'payment_id' => $payment->id,
            'paid_at' => now(),
         ]);

         return redirect()->route('orders.show', $order)
            ->with('success', 'Paiement confirmé avec succès !');
      } catch (\Exception $e) {
         return back()->with('error', 'Une erreur est survenue lors de la confirmation du paiement.');
      }
   }

   public function cancel(Order $order)
   {
      if ($order->status === 'pending') {
         $order->update(['status' => 'cancelled']);
         return redirect()->route('orders.index')
            ->with('success', 'Commande annulée avec succès.');
      }

      return back()->with('error', 'Cette commande ne peut plus être annulée.');
   }
}
