<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Inertia\Inertia;

class ProfileController extends Controller
{
   public function edit(Request $request): \Inertia\Response
   {
      $user = $request->user();
      $addresses = []; // À remplacer par la vraie récupération
      $orders = []; // À remplacer par la vraie récupération

      $shop = null;
      $shopProducts = [];
      $shopOrders = [];
      $shopStats = [];
      $commissions = [];
      $partnerships = [];

      if ($user->role === 'shop') {
         $shop = $user->shop ?? null;
         $shopProducts = $shop ? $shop->products()->orderByDesc('created_at')->take(5)->get() : [];
         $shopOrders = $shop ? $shop->orders()->orderByDesc('created_at')->take(5)->get() : [];
         // Statistiques détaillées
         $shopStats = [
            'total_sales' => $shop->total_sales ?? 0,
            'total_orders' => $shop->total_orders ?? 0,
            'total_products' => $shop->total_products ?? 0,
            'rating' => $shop->rating ?? 0,
            'review_count' => $shop->review_count ?? 0,
         ];
      } elseif ($user->role === 'promoter') {
         $commissions = $user->commissions()->get() ?? [];
         $partnerships = $user->partnerships()->get() ?? [];
      }

      return Inertia::render('Profile/Index', [
         'user' => [
            'firstName' => $user->name, // À adapter si prénom/nom séparés
            'lastName' => '', // À adapter si prénom/nom séparés
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
         ],
         'addresses' => $addresses,
         'orders' => $orders,
         'shop' => $shop,
         'shopProducts' => $shopProducts,
         'shopOrders' => $shopOrders,
         'shopStats' => $shopStats,
         'commissions' => $commissions,
         'partnerships' => $partnerships,
         'mustVerifyEmail' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
         'status' => session('status'),
      ]);
   }

   public function update(ProfileUpdateRequest $request): RedirectResponse
   {
      Auth::user()->fill($request->validated());

      if (Auth::user()->isDirty('email')) {
         Auth::user()->email_verified_at = null;
      }

      Auth::user()->save();

      return Redirect::route('profile.edit')->with('status', 'profile-updated');
   }

   public function destroy(Request $request): RedirectResponse
   {
      $request->validateWithBag('userDeletion', [
         'password' => ['required', 'current_password'],
      ]);

      $user = $request->user();
      Auth::logout();

      $user->delete();

      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return Redirect::to('/');
   }
}
