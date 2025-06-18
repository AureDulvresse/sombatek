<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth')->except(['show']);
      $this->middleware('role:shop')->only(['create', 'store', 'edit', 'update', 'destroy']);
   }

   public function index(Request $request)
   {
      $query = Advertisement::with('shop')
         ->when($request->has('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
         })
         ->when($request->has('position'), function ($q) use ($request) {
            $q->where('position', $request->position);
         });

      $advertisements = $query->paginate(10)->withQueryString();

      return Inertia::render('Advertisements/Index', [
         'advertisements' => $advertisements,
         'filters' => $request->only(['status', 'position'])
      ]);
   }

   public function create()
   {
      $positions = [
         'home_top' => 'En-tête de la page d\'accueil',
         'home_sidebar' => 'Barre latérale de la page d\'accueil',
         'category_top' => 'En-tête des catégories',
         'product_sidebar' => 'Barre latérale des produits',
         'search_results' => 'Résultats de recherche',
      ];

      return Inertia::render('Advertisements/Create', [
         'positions' => $positions
      ]);
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'title' => 'required|string|max:255',
         'description' => 'nullable|string',
         'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
         'link' => 'required|url',
         'position' => 'required|string',
         'targeting' => 'nullable|array',
         'budget' => 'required|numeric|min:0',
         'start_date' => 'required|date|after:today',
         'end_date' => 'required|date|after:start_date',
      ]);

      $shop = Auth::user()->shop;
      if (!$shop) {
         return back()->with('error', 'Vous devez avoir une boutique pour créer une publicité.');
      }

      $advertisement = new Advertisement($validated);
      $advertisement->shop_id = $shop->id;
      $advertisement->status = 'pending';
      $advertisement->price = $this->calculatePrice($validated['position'], $validated['start_date'], $validated['end_date']);

      if ($request->hasFile('image')) {
         $path = $request->file('image')->store('advertisements', 'public');
         $advertisement->image = $path;
      }

      $advertisement->save();

      return redirect()->route('advertisements.index')
         ->with('success', 'Publicité créée avec succès.');
   }

   public function show(Advertisement $advertisement)
   {
      $advertisement->incrementImpressions();
      return redirect($advertisement->link);
   }

   public function edit(Advertisement $advertisement)
   {
      $this->authorize('update', $advertisement);

      $positions = [
         'home_top' => 'En-tête de la page d\'accueil',
         'home_sidebar' => 'Barre latérale de la page d\'accueil',
         'category_top' => 'En-tête des catégories',
         'product_sidebar' => 'Barre latérale des produits',
         'search_results' => 'Résultats de recherche',
      ];

      return Inertia::render('Advertisements/Edit', [
         'advertisement' => $advertisement,
         'positions' => $positions
      ]);
   }

   public function update(Request $request, Advertisement $advertisement)
   {
      $this->authorize('update', $advertisement);

      $validated = $request->validate([
         'title' => 'required|string|max:255',
         'description' => 'nullable|string',
         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
         'link' => 'required|url',
         'position' => 'required|string',
         'targeting' => 'nullable|array',
         'budget' => 'required|numeric|min:0',
         'start_date' => 'required|date',
         'end_date' => 'required|date|after:start_date',
         'status' => 'required|in:pending,active,paused,completed',
      ]);

      if ($request->hasFile('image')) {
         Storage::disk('public')->delete($advertisement->image);
         $path = $request->file('image')->store('advertisements', 'public');
         $validated['image'] = $path;
      }

      $advertisement->update($validated);

      return redirect()->route('advertisements.index')
         ->with('success', 'Publicité mise à jour avec succès.');
   }

   public function destroy(Advertisement $advertisement)
   {
      $this->authorize('delete', $advertisement);

      Storage::disk('public')->delete($advertisement->image);
      $advertisement->delete();

      return redirect()->route('advertisements.index')
         ->with('success', 'Publicité supprimée avec succès.');
   }

   public function click(Advertisement $advertisement)
   {
      $advertisement->incrementClicks();
      $advertisement->update(['ctr' => ($advertisement->clicks / $advertisement->impressions) * 100]);
      return redirect($advertisement->link);
   }

   private function calculatePrice($position, $startDate, $endDate)
   {
      $basePrices = [
         'home_top' => 100,
         'home_sidebar' => 50,
         'category_top' => 75,
         'product_sidebar' => 60,
         'search_results' => 40,
      ];

      $days = now()->parse($endDate)->diffInDays(now()->parse($startDate));
      return $basePrices[$position] * $days;
   }
}
