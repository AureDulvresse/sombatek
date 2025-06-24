<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
   public function dashboard()
   {
      // Statistiques de base
      $stats = [
         'users' => User::count(),
         'shops' => Shop::count(),
         'products' => Product::count(),
         'orders' => Order::count(),
         'revenue' => Order::sum('total'),
         'pendingShops' => Shop::where('status', 'pending')->count(),
         'lowStockProducts' => Product::where('stock', '<=', 5)->count(),
      ];

      // Statistiques mensuelles (6 derniers mois)
      $monthlyStats = DB::table('orders')
         ->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total) as revenue')
         )
         ->where('created_at', '>=', now()->subMonths(6))
         ->groupBy('month')
         ->orderBy('month')
         ->get();

      $stats['monthlyStats'] = [
         'labels' => $monthlyStats->pluck('month')->toArray(),
         'orders' => $monthlyStats->pluck('order_count')->toArray(),
         'revenue' => $monthlyStats->pluck('revenue')->toArray(),
      ];

      // Alertes
      $alerts = [];

      // Alerte pour les boutiques en attente
      if ($stats['pendingShops'] > 0) {
         $alerts[] = [
            'type' => 'warning',
            'message' => "{$stats['pendingShops']} boutiques en attente de validation",
            'action' => 'Voir les boutiques',
            'link' => route('shops.pending')
         ];
      }

      // Alerte pour les produits en stock faible
      if ($stats['lowStockProducts'] > 0) {
         $alerts[] = [
            'type' => 'warning',
            'message' => "{$stats['lowStockProducts']} produits en stock faible",
            'action' => null,
            'link' => null
         ];
      }

      return Inertia::render('Admin/Dashboard', [
         'stats' => $stats,
         'recentUsers' => User::latest()->take(5)->get(['id', 'name', 'email']),
         'recentShops' => Shop::latest()->take(5)->get(['id', 'name', 'email']),
         'recentOrders' => Order::with('user')->latest()->take(5)->get(),
         'alerts' => $alerts,
      ]);
   }

   public function analytics()
   {
      return Inertia::render('Admin/Analytics');
   }

   public function reports()
   {
      return Inertia::render('Admin/Reports');
   }
}
