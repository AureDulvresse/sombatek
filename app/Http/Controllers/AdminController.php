<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
   public function dashboard()
   {
      return Inertia::render('Admin/Dashboard');
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
