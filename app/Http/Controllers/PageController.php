<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function home()
    {
        // Récupérer les catégories populaires avec mise en cache
        $categories = Cache::remember('popular_categories', 3600, function () {
            return Category::popular()
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->take(12)
                ->get();
        });

        // Récupérer les nouveaux produits
        $newArrivals = Cache::remember('new_arrivals', 1800, function () {
            return Product::newArrivals()
                ->with(['shop', 'images'])
                ->get();
        });

        // Récupérer les meilleures ventes
        $bestSellers = Cache::remember('best_sellers', 1800, function () {
            return Product::bestSellers()
                ->with(['shop', 'images'])
                ->get();
        });

        // Récupérer les offres saisonnières
        $seasonalDeals = Cache::remember('seasonal_deals', 1800, function () {
            return Product::seasonalDeals()
                ->with(['shop', 'images'])
                ->get();
        });

        // Récupérer les offres flash
        $flashDeals = Cache::remember('flash_deals', 900, function () {
            return Product::flashDeals()
                ->with(['shop', 'images'])
                ->get();
        });

        // Récupérer les produits tendance
        $trendingProducts = Cache::remember('trending_products', 1800, function () {
            return Product::trending()
                ->with(['shop', 'images'])
                ->take(9)
                ->get();
        });

        // Récupérer les boutiques populaires
        $topShops = Cache::remember('top_shops', 3600, function () {
            return Shop::mostPopular()
                ->with(['products' => function ($query) {
                    $query->active()->inStock();
                }])
                ->take(12)
                ->get();
        });


        return Inertia::render('Home', [
            'categories' => $categories,
            'newArrivals' => $newArrivals,
            'bestSellers' => $bestSellers,
            'seasonalDeals' => $seasonalDeals,
            'flashDeals' => $flashDeals,
            'trendingProducts' => $trendingProducts,
            'topShops' => $topShops,
        ]);
    }
    public function help()
    {
        return Inertia::render('Help');
    }

    public function contact()
    {
        return Inertia::render('Contact');
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        // TODO: Envoyer l'email ou stocker le message dans la base de données

        return redirect()->back()->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
    }

    public function faq()
    {
        return Inertia::render('Faq');
    }

    public function shipping()
    {
        return Inertia::render('Shipping');
    }

    public function about()
    {
        return Inertia::render('About');
    }

    public function blog()
    {
        return Inertia::render('Blog');
    }

    public function careers()
    {
        return Inertia::render('Careers');
    }

    public function partners()
    {
        return Inertia::render('Partners');
    }

    public function legal()
    {
        return Inertia::render('Legal');
    }

    public function privacy()
    {
        return Inertia::render('Privacy');
    }

    public function terms()
    {
        return Inertia::render('Terms');
    }

    public function cookies()
    {
        return Inertia::render('Cookies');
    }
}
