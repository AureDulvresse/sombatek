<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartUpdateRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
   public function index()
   {
      $cart = $this->getOrCreateCart();
      $cart->load(['items.product']);

      return Inertia::render('Cart/Index', [
         'cart' => [
            'items' => $cart->items->map(function ($item) {
               return [
                  'id' => $item->id,
                  'product_id' => $item->product_id,
                  'name' => $item->name,
                  'price' => $item->price,
                  'quantity' => $item->quantity,
                  'subtotal' => $item->price * $item->quantity,
                  'default_image' => $item->default_image,
                  'options' => $item->options,
                  'product' => [
                     'stock' => $item->product->stock,
                     'is_active' => $item->product->is_active,
                  ]
               ];
            }),
            'total' => $cart->total,
            'discount_total' => $cart->discount_total,
            'promotion_code' => $cart->promotion_code,
            'promotion_discount' => $cart->promotion_discount,
            'items_count' => $cart->getItemCount()
         ]
      ]);
   }

   public function addItem(CartUpdateRequest $request): JsonResponse
   {
      try {
         $data = $request->validated();
         $product = Product::findOrFail($data['product_id']);
         $quantityRequest = $data['quantity'];

         // Vérifications de stock et disponibilité
         if (!$this->validateProductAvailability($product, $quantityRequest)) {
            return response()->json([
               'success' => false,
               'message' => 'Produit non disponible ou stock insuffisant.'
            ], 422);
         }

         $cart = $this->getOrCreateCart();
         $item = $cart->addItem($product, $quantityRequest, $data['options'] ?? []);

         return $this->getCartResponse($cart, 'Article ajouté au panier');
      } catch (\Exception $e) {
         return $this->getErrorResponse($e);
      }
   }

   public function removeItem(Request $request): JsonResponse
   {
      try {
         $request->validate(['item_id' => 'required|exists:cart_items,id']);
         $cart = $this->getOrCreateCart();

         if ($cart->removeItem($request->item_id)) {
            return $this->getCartResponse($cart, 'Produit retiré du panier');
         }

         return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression'
         ], 422);
      } catch (\Exception $e) {
         return $this->getErrorResponse($e);
      }
   }

   public function updateQuantity(Request $request): JsonResponse
   {
      try {
         $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
         ]);

         $cart = $this->getOrCreateCart();
         $item = $cart->updateItemQuantity($request->product_id, $request->quantity);

         if ($item) {
            return $this->getCartResponse($cart, 'Quantité mise à jour');
         }

         return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour'
         ], 422);
      } catch (\Exception $e) {
         return $this->getErrorResponse($e);
      }
   }

   public function applyPromotion(Request $request): JsonResponse
   {
      try {
         $request->validate(['code' => 'required|string']);
         $cart = $this->getOrCreateCart();

         $cart->update(['promotion_code' => $request->code]);
         $cart->updateTotals();

         return $this->getCartResponse($cart, 'Code promotion appliqué');
      } catch (\Exception $e) {
         return $this->getErrorResponse($e);
      }
   }

   public function removePromotion(): JsonResponse
   {
      try {
         $cart = $this->getOrCreateCart();
         $cart->update([
            'promotion_code' => null,
            'promotion_discount' => 0
         ]);
         $cart->updateTotals();

         return $this->getCartResponse($cart, 'Code promotion retiré');
      } catch (\Exception $e) {
         return $this->getErrorResponse($e);
      }
   }

   public function clear(): JsonResponse
   {
      try {
         $cart = $this->getOrCreateCart();
         $cart->clear();

         return response()->json([
            'success' => true,
            'message' => 'Panier vidé',
            'cart' => [
               'items' => [],
               'total' => 0,
               'discount_total' => 0,
               'promotion_discount' => 0,
               'items_count' => 0
            ]
         ]);
      } catch (\Exception $e) {
         return $this->getErrorResponse($e);
      }
   }

   public function getCart(): JsonResponse
   {
      try {
         $cart = $this->getOrCreateCart();
         $cart->load(['items' => function ($query) {
            $query->with('product:id,name,stock,is_active,sale_price,price');
         }]);
         return $this->getCartResponse($cart, 'Panier récupéré');
      } catch (\Exception $e) {
         Log::error('Erreur Cart::get: ' . $e->getMessage());
         return $this->getErrorResponse($e);
      }
   }

   protected function getOrCreateCart(): Cart
   {
      if (Auth::check()) {
         $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            [
               'session_id' => Session::getId(),
               'status' => 'active',
               'total' => 0,
               'discount_total' => 0,
               'promotion_discount' => 0
            ]
         );

         // Synchroniser avec le panier de session si nécessaire
         if ($cart->session_id !== Session::getId()) {
            $sessionCart = Cart::where('session_id', Session::getId())->first();
            if ($sessionCart) {
               $cart->syncWithUser(Auth::user());
            }
         }

         return $cart;
      }

      return Cart::firstOrCreate(
         ['session_id' => Session::getId()],
         [
            'status' => 'active',
            'total' => 0,
            'discount_total' => 0,
            'promotion_discount' => 0
         ]
      );
   }

   protected function validateProductAvailability(Product $product, int $quantity): bool
   {
      if (!$product->is_active) {
         return false;
      }

      if ($product->stock < $quantity) {
         return false;
      }

      return true;
   }

   protected function getCartResponse(Cart $cart, string $message): JsonResponse
   {
      return response()->json([
         'success' => true,
         'message' => $message,
         'cart' => [
            'items' => $cart->items->map(function ($item) {
               return [
                  'id' => $item->id,
                  'product_id' => $item->product_id,
                  'name' => $item->name,
                  'price' => $item->price,
                  'quantity' => $item->quantity,
                  'subtotal' => $item->price * $item->quantity,
                  'default_image' => $item->default_image,
                  'options' => $item->options
               ];
            }),
            'total' => $cart->total,
            'discount_total' => $cart->discount_total,
            'promotion_code' => $cart->promotion_code,
            'promotion_discount' => $cart->promotion_discount,
            'items_count' => $cart->getItemCount()
         ]
      ]);
   }

   protected function getErrorResponse(\Exception $e): JsonResponse
   {
      return response()->json([
         'success' => false,
         'message' => 'Une erreur est survenue : ' . $e->getMessage()
      ], 500);
   }
}
