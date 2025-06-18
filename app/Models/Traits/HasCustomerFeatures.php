<?php

namespace App\Models\Traits;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

trait HasCustomerFeatures
{
   public function cart(): HasOne
   {
      return $this->hasOne(Cart::class);
   }

    /**
     * Get the user's wishlist items.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

   public function orders(): HasMany
   {
      return $this->hasMany(Order::class);
   }

   public function getCart(): Cart
   {
      if (Auth::check()) {
         return Cart::firstOrCreate(
            ['user_id' => $this->id],
            ['status' => 'active']
         );
      }

      return Cart::firstOrCreate(
         ['session_id' => session()->getId()],
         ['status' => 'active']
      );
   }

    public function getFavoriteProducts(): Wishlist
    {
        if (Auth::check()) {
            return Wishlist::firstOrCreate(
                ['user_id' => $this->id],
                [
                    'session_id' => session()->getId(),
                    'status' => 'active'
                    ]
            );
        }

        return Wishlist::firstOrCreate(
            ['session_id' => session()->getId()],
            ['status' => 'active']
        );
    }

   public function getActiveOrders()
   {
      return $this->orders()
         ->whereIn('status', ['pending', 'processing', 'shipped'])
         ->latest()
         ->get();
   }

   public function getOrderHistory()
   {
      return $this->orders()
         ->whereIn('status', ['delivered', 'cancelled'])
         ->latest()
         ->get();
   }

   public function getTotalSpent(): float
   {
      return $this->orders()
         ->where('status', 'delivered')
         ->sum('total');
   }

   public function getRecentOrders(int $limit = 5)
   {
      return $this->orders()
         ->latest()
         ->take($limit)
         ->get();
   }
}
