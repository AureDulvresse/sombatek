<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AddressController extends Controller
{
   public function index()
   {
      $addresses = Auth::user()->addresses;
      return Inertia::render('Profile/Addresses/Index', [
         'addresses' => $addresses
      ]);
   }

   public function create()
   {
      return Inertia::render('Profile/Addresses/Create');
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'first_name' => 'required|string|max:255',
         'last_name' => 'required|string|max:255',
         'address' => 'required|string|max:255',
         'postal_code' => 'required|string|max:10',
         'city' => 'required|string|max:255',
         'country' => 'required|string|max:255',
         'phone' => 'required|string|max:20',
         'is_default' => 'boolean'
      ]);

      $address = Auth::user()->addresses()->create($validated);

      if ($validated['is_default']) {
         Auth::user()->addresses()->where('id', '!=', $address->id)
            ->update(['is_default' => false]);
      }

      return redirect()->route('profile.addresses.index')
         ->with('success', 'Adresse ajoutée avec succès.');
   }

   public function edit(Address $address)
   {
      $this->authorize('update', $address);

      return Inertia::render('Profile/Addresses/Edit', [
         'address' => $address
      ]);
   }

   public function update(Request $request, Address $address)
   {
      $this->authorize('update', $address);

      $validated = $request->validate([
         'first_name' => 'required|string|max:255',
         'last_name' => 'required|string|max:255',
         'address' => 'required|string|max:255',
         'postal_code' => 'required|string|max:10',
         'city' => 'required|string|max:255',
         'country' => 'required|string|max:255',
         'phone' => 'required|string|max:20',
         'is_default' => 'boolean'
      ]);

      $address->update($validated);

      if ($validated['is_default']) {
         auth()->user()->addresses()->where('id', '!=', $address->id)
            ->update(['is_default' => false]);
      }

      return redirect()->route('profile.addresses.index')
         ->with('success', 'Adresse mise à jour avec succès.');
   }

   public function destroy(Address $address)
   {
      $this->authorize('delete', $address);

      $address->delete();

      return redirect()->route('profile.addresses.index')
         ->with('success', 'Adresse supprimée avec succès.');
   }
}
