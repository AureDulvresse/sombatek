<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
      $this->middleware('role:admin');
   }

   public function index()
   {
      $users = User::with(['shop', 'subscriptions'])
         ->latest()
         ->paginate(10);

      return Inertia::render('Users/Index', [
         'users' => $users
      ]);
   }

   public function create()
   {
      return Inertia::render('Users/Create');
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users',
         'password' => ['required', Password::defaults()],
         'role' => 'required|in:admin,shop,promoter,customer',
         'phone' => 'nullable|string|max:20',
         'address' => 'nullable|string|max:255',
         'city' => 'nullable|string|max:100',
         'country' => 'nullable|string|max:100',
         'postal_code' => 'nullable|string|max:20',
      ]);

      $user = User::create([
         'name' => $validated['name'],
         'email' => $validated['email'],
         'password' => Hash::make($validated['password']),
         'role' => $validated['role'],
         'phone' => $validated['phone'],
         'address' => $validated['address'],
         'city' => $validated['city'],
         'country' => $validated['country'],
         'postal_code' => $validated['postal_code'],
      ]);

      return redirect()->route('users.index')
         ->with('success', 'Utilisateur créé avec succès.');
   }

   public function edit(User $user)
   {
      return Inertia::render('Users/Edit', [
         'user' => $user->load(['shop', 'subscriptions'])
      ]);
   }

   public function update(Request $request, User $user)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
         'role' => 'required|in:admin,shop,promoter,customer',
         'phone' => 'nullable|string|max:20',
         'address' => 'nullable|string|max:255',
         'city' => 'nullable|string|max:100',
         'country' => 'nullable|string|max:100',
         'postal_code' => 'nullable|string|max:20',
         'is_active' => 'boolean',
      ]);

      $user->update($validated);

      return redirect()->route('users.index')
         ->with('success', 'Utilisateur mis à jour avec succès.');
   }

   public function destroy(User $user)
   {
      if ($user->id === Auth::user()->id()) {
         return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
      }

      $user->delete();

      return redirect()->route('users.index')
         ->with('success', 'Utilisateur supprimé avec succès.');
   }

   public function toggleStatus(User $user)
   {
      $user->update(['is_active' => !$user->is_active]);

      return back()->with(
         'success',
         $user->is_active
            ? 'Utilisateur activé avec succès.'
            : 'Utilisateur désactivé avec succès.'
      );
   }
}
