<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShopPolicy
{
   use HandlesAuthorization;

   public function viewAny(?User $user): bool
   {
      return true;
   }

   public function view(?User $user, Shop $shop): bool
   {
      return true;
   }

   public function create(User $user): bool
   {
      return $user->isShop();
   }

   public function update(User $user, Shop $shop): bool
   {
      return $user->isAdmin() || $user->id === $shop->user_id;
   }

   public function delete(User $user, Shop $shop): bool
   {
      return $user->isAdmin();
   }

   public function restore(User $user, Shop $shop): bool
   {
      return $user->isAdmin();
   }

   public function forceDelete(User $user, Shop $shop): bool
   {
      return $user->isAdmin();
   }
}
