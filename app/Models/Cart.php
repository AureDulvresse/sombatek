<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'session_id',
      'status',
      'total',
      'discount_total',
      'promotion_code',
      'promotion_discount',
   ];

   protected $casts = [
      'total' => 'decimal:2',
      'discount_total' => 'decimal:2',
      'promotion_discount' => 'decimal:2',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }

   public function items(): HasMany
   {
      return $this->hasMany(CartItem::class);
   }

   public function addItem(Product $product, int $quantity = 1, array $options = []): CartItem
   {
      $item = $this->items()->updateOrCreate(
         ['product_id' => $product->id],
         [
            'name' => $product->name,
            'price' => $product->sale_price ?? $product->price,
            'quantity' => $quantity,
            'options' => $options,
            'default_image' => $product->default_image,
         ]
      );

      $this->updateTotals();
      return $item;
   }

   public function removeItem(int $itemId): bool
   {
      $removed = $this->items()->where('id', $itemId)->delete();
      if ($removed) {
         $this->updateTotals();
      }
      return $removed;
   }

   public function updateItemQuantity(int $productId, int $quantity): ?CartItem
   {
      $item = $this->items()->where('product_id', $productId)->first();
      if ($item) {
         $item->update(['quantity' => $quantity]);
         $this->updateTotals();
      }
      return $item;
   }

   public function clear(): void
   {
      $this->items()->delete();
      $this->update(['total' => 0, 'discount_total' => 0, 'promotion_discount' => 0]);
   }

   public function getItemCount(): int
   {
      return $this->items()->sum('quantity');
   }

   public function updateTotals(): void
   {
      $subtotal = $this->items()->sum(DB::raw('price * quantity'));
      $discount = $this->calculateDiscounts();

      $this->update([
         'total' => $subtotal - $discount,
         'discount_total' => $discount
      ]);
   }

   protected function calculateDiscounts(): float
   {
      $discount = 0;

      // Calculer les réductions par produit
      foreach ($this->items as $item) {
         if ($item->product->isOnSale) {
            $discount += ($item->product->price - $item->product->sale_price) * $item->quantity;
         }
      }

      // Appliquer le code promo si existant
      if ($this->promotion_code) {
         $promotion = Promotion::where('code', $this->promotion_code)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

         if ($promotion) {
            $this->promotion_discount = $promotion->calculateDiscount($this->total);
            $discount += $this->promotion_discount;
         }
      }

      return $discount;
   }

   public function syncWithUser(User $user): void
   {
      if ($this->user_id !== $user->id) {
         // Fusionner les paniers
         $userCart = $user->cart;
         if ($userCart) {
            foreach ($this->items as $item) {
               $userCart->addItem($item->product, $item->quantity, $item->options);
            }
            $this->delete();
         } else {
            $this->update(['user_id' => $user->id]);
         }
      }
   }
}
