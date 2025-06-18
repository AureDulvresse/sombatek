<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
   public function handle(Request $request, Closure $next, string $role): Response
   {
      if (!$request->user() || !$request->user()->hasRole($role)) {
         if ($request->expectsJson()) {
            return response()->json([
               'success' => false,
               'message' => 'Accès non autorisé'
            ], 403);
         }
         return redirect()->route('home')->with('error', 'Accès non autorisé');
      }

      return $next($request);
   }
}
