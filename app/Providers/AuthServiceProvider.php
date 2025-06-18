<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Shop;
use App\Policies\ProductPolicy;
use App\Policies\ShopPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
   protected $policies = [
      Shop::class => ShopPolicy::class,
      Product::class => ProductPolicy::class,
   ];

   public function boot(): void
   {
      $this->registerPolicies();

      Gate::before(function ($user, $ability) {
         return $user->isAdmin() ? true : null;
      });
   }
}
