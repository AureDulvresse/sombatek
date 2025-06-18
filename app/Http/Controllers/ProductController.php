<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Advertisement;

class ProductController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth')->except(['index', 'show']);
      $this->middleware('role:shop')->only(['create', 'store', 'edit', 'update', 'destroy', 'catalog']);
   }

   public function index(Request $request)
   {
      $products = Product::getAllUsedAlgorithm();

      // // Filtres de base
      // if ($request->has('category')) {
      //    $query->where('category_id', $request->category);
      // }

      // if ($request->has('shop')) {
      //    $query->where('shop_id', $request->shop);
      // }

      // // Filtre de prix
      // if ($request->has('min_price')) {
      //    $query->where('price', '>=', $request->min_price);
      // }
      // if ($request->has('max_price')) {
      //    $query->where('price', '<=', $request->max_price);
      // }

      // // Filtre de disponibilité
      // if ($request->has('in_stock')) {
      //    $query->where('stock', '>', 0);
      // }

      // // Filtre de recherche avancée
      // if ($request->has('search')) {
      //    $search = $request->search;
      //    $query->where(function ($q) use ($search) {
      //       $q->where('name', 'like', "%{$search}%")
      //          ->orWhere('description', 'like', "%{$search}%")
      //          ->orWhere('sku', 'like', "%{$search}%");
      //    });
      // }

      // // Filtre de note
      // if ($request->has('min_rating')) {
      //    $query->whereHas('reviews', function ($q) use ($request) {
      //       $q->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
      //    });
      // }

      // // Tri
      // $sort = $request->get('sort', 'created_at');
      // $direction = $request->get('direction', 'desc');

      // // Options de tri personnalisées
      // $sortOptions = [
      //    'price_asc' => ['price', 'asc'],
      //    'price_desc' => ['price', 'desc'],
      //    'name_asc' => ['name', 'asc'],
      //    'name_desc' => ['name', 'desc'],
      //    'rating' => ['rating', 'desc'],
      //    'newest' => ['created_at', 'desc'],
      //    'popular' => ['views', 'desc'],
      // ];

      // if (isset($sortOptions[$sort])) {
      //    [$sort, $direction] = $sortOptions[$sort];
      // }

      // $query->orderBy($sort, $direction);

      // // Pagination avec options de nombre d'éléments par page
      // $perPage = $request->get('per_page', 12);
      // $products = $query->paginate($perPage)->withQueryString();

      // Chargement des données supplémentaires
      $categories = Category::withCount('products')->get();
      $featuredProducts = Product::where('is_featured', true)
         ->where('is_active', true)
         ->take(4)
         ->get();

      // Récupération des publicités pertinentes
      // $advertisements = Advertisement::where('status', 'active')
      //    ->where('start_date', '<=', now())
      //    ->where('end_date', '>=', now())
      //    ->where(function ($query) use ($request) {
      //       $query->whereNull('targeting')
      //          ->orWhereJsonContains('targeting->categories', $request->category);
      //    })
      //    ->where('position', 'product_sidebar')
      //    ->take(3)
      //    ->get();

      return Inertia::render('Products/List', [
         'products' => $products->toArray(),
         'categories' => $categories,
         'featuredProducts' => $featuredProducts,
         // 'advertisements' => $advertisements,
         'advertisements' => [],
         'filters' => $request->only([
            'search',
            'category',
            'shop',
            'min_price',
            'max_price',
            'in_stock',
            'min_rating',
            'sort',
            'direction',
            'per_page'
         ])
      ]);
   }

   public function create()
   {
      $categories = Category::all();
      return Inertia::render('Products.create', [
         'categories' => $categories
      ]);
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'required|string',
         'price' => 'required|numeric|min:0',
         'stock' => 'required|integer|min:0',
         'category_id' => 'required|exists:categories,id',
         'images' => 'required|array|min:1',
         'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
         'is_active' => 'boolean',
         'is_featured' => 'boolean',
         'sku' => 'nullable|string|max:50|unique:products',
         'weight' => 'nullable|numeric|min:0',
         'dimensions' => 'nullable|array',
      ]);

      $shop = Auth::user()->shop;

      if (!$shop) {
         return back()->with('error', 'Vous devez avoir une boutique pour ajouter des produits.');
      }

      $product = new Product($validated);
      $product->shop_id = $shop->id;
      $product->slug = Str::slug($validated['name']);

      if (!isset($validated['sku'])) {
         $product->sku = Str::upper(Str::random(8));
      }

      $product->save();

      // Gestion des images
      if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['path' => $path]);
         }
      }

      return redirect()->route('products.show', $product)
         ->with('success', 'Produit créé avec succès.');
   }

   public function show(Product $product)
   {
      $product->load(['shop', 'category', 'images']);

      return Inertia::render('Products/Show', [
         'product' => $product
      ]);
   }

   public function edit(Product $product)
   {
      $this->authorize('update', $product);

      $product->load(['images']);
      $categories = Category::all();

      return Inertia::render('Products/Edit', [
         'product' => $product,
         'categories' => $categories
      ]);
   }

   public function update(Request $request, Product $product)
   {
      $this->authorize('update', $product);

      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'required|string',
         'price' => 'required|numeric|min:0',
         'stock' => 'required|integer|min:0',
         'category_id' => 'required|exists:categories,id',
         'images' => 'nullable|array',
         'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
         'is_active' => 'boolean',
         'is_featured' => 'boolean',
         'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
         'weight' => 'nullable|numeric|min:0',
         'dimensions' => 'nullable|array',
      ]);

      $product->update($validated);

      // Gestion des nouvelles images
      if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['path' => $path]);
         }
      }

      return redirect()->route('products.show', $product)
         ->with('success', 'Produit mis à jour avec succès.');
   }

   public function destroy(Product $product)
   {
      $this->authorize('delete', $product);

      // Suppression des images
      foreach ($product->images as $image) {
         Storage::disk('public')->delete($image->path);
         $image->delete();
      }

      $product->delete();

      return redirect()->route('dashboard')
         ->with('success', 'Produit supprimé avec succès.');
   }

   public function toggleStatus(Product $product)
   {
      $this->authorize('update', $product);

      $product->update(['is_active' => !$product->is_active]);

      return back()->with(
         'success',
         $product->is_active
            ? 'Produit activé avec succès.'
            : 'Produit désactivé avec succès.'
      );
   }
}
