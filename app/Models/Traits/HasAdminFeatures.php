<?php

namespace App\Models\Traits;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait HasAdminFeatures
{
   public function getDashboardStats()
   {
      return [
         'total_users' => User::count(),
         'total_shops' => Shop::count(),
         'total_products' => Product::count(),
         'total_orders' => Order::count(),
         'total_revenue' => Order::where('status', 'delivered')->sum('total'),
         'pending_orders' => Order::where('status', 'pending')->count(),
         'active_shops' => Shop::where('status', 'active')->count(),
         'low_stock_products' => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
      ];
   }

   public function getRecentOrdersForAdmin(int $limit = 10)
   {
      return Order::with(['user', 'items.product'])
         ->latest()
         ->take($limit)
         ->get();
   }

   public function getTopSellingProductsForAdmin(int $limit = 5)
   {
      return Product::withCount(['orderItems as total_sales' => function ($query) {
         $query->whereHas('order', function ($q) {
            $q->where('status', 'delivered');
         });
      }])
         ->orderByDesc('total_sales')
         ->take($limit)
         ->get();
   }

   public function getTopPerformingShops(int $limit = 5)
   {
      return Shop::withCount(['orders as total_orders' => function ($query) {
         $query->where('status', 'delivered');
      }])
         ->withSum(['orders as total_revenue' => function ($query) {
            $query->where('status', 'delivered');
         }], 'total')
         ->orderByDesc('total_revenue')
         ->take($limit)
         ->get();
   }

   public function getMonthlyRevenue()
   {
      return Order::where('status', 'delivered')
         ->whereYear('created_at', now()->year)
         ->whereMonth('created_at', now()->month)
         ->sum('total');
   }

   public function getYearlyRevenue()
   {
      return Order::where('status', 'delivered')
         ->whereYear('created_at', now()->year)
         ->sum('total');
   }

   public function getRevenueByMonth()
   {
      return Order::where('status', 'delivered')
         ->whereYear('created_at', now()->year)
         ->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total')
         )
         ->groupBy('month')
         ->orderBy('month')
         ->get();
   }

   public function getPendingVerifications()
   {
      return Shop::where('is_verified', false)
         ->where('verification_status', 'pending')
         ->latest()
         ->get();
   }

   public function getSystemHealth()
   {
      return [
         'database_size' => $this->getDatabaseSize(),
         'cache_status' => $this->getCacheStatus(),
         'queue_status' => $this->getQueueStatus(),
         'storage_usage' => $this->getStorageUsage(),
      ];
   }

   protected function getDatabaseSize()
   {
      // TODO: Implémenter la logique pour obtenir la taille de la base de données
      return 0;
   }

   protected function getCacheStatus()
   {
      // TODO: Implémenter la logique pour vérifier le statut du cache
      return true;
   }

   protected function getQueueStatus()
   {
      // TODO: Implémenter la logique pour vérifier le statut de la file d'attente
      return true;
   }

   protected function getStorageUsage()
   {
      // TODO: Implémenter la logique pour obtenir l'utilisation du stockage
      return 0;
   }
}
