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
      return Inertia::render('Profile/Edit', [
         'mustVerifyEmail' => $request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
         'status' => session('status'),
      ]);
   }

   public function update(ProfileUpdateRequest $request): RedirectResponse
   {
      $request->user()->fill($request->validated());

      if ($request->user()->isDirty('email')) {
         $request->user()->email_verified_at = null;
      }

      $request->user()->save();

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
