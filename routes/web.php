<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// =============================================
// Routes publiques (Guest)
// =============================================
Route::get('/', [PageController::class, 'home'])->name('home');

// Pages statiques
Route::get('/help', [PageController::class, 'help'])->name('help');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/shipping', [PageController::class, 'shipping'])->name('shipping');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::get('/partners', [PageController::class, 'partners'])->name('partners');
Route::get('/legal', [PageController::class, 'legal'])->name('legal');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');

// Catalogue et produits
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

// =============================================
// Routes authentifiées (Auth)
// =============================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // =============================================
    // Routes pour les clients
    // =============================================
    Route::middleware(['role:customer'])->group(function () {
        // Panier
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('cart.index');
            Route::post('/add-item', [CartController::class, 'addItem'])->name('cart.add-item');
            Route::post('/remove-item', [CartController::class, 'removeItem'])->name('cart.remove-item');
            Route::post('/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
            Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
            Route::get('/get', [CartController::class, 'getCart'])->name('cart.get');
            Route::post('/apply-promotion', [CartController::class, 'applyPromotion'])->name('cart.apply-promotion');
            Route::post('/remove-promotion', [CartController::class, 'removePromotion'])->name('cart.remove-promotion');
        });

        // Liste de souhaits
        Route::prefix('wishlist')->group(function () {
            Route::get('/get', [WishlistController::class, 'get'])->name('wishlist.get');
            Route::post('/toggle-item', [WishlistController::class, 'toggleItem'])->name('wishlist.toggle-item');
            Route::post('/remove-item', [WishlistController::class, 'removeItem'])->name('wishlist.remove-item');
            Route::post('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
            Route::post('/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
        });

        // Commandes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/{order}/return', [OrderController::class, 'return'])->name('orders.return');
        });

        // Profil client
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
            Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
            Route::get('/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
            Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
            Route::resource('addresses', AddressController::class);
        });

        // Routes spécifiques aux clients
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
    });

    // =============================================
    // Routes pour les vendeurs
    // =============================================
    Route::middleware(['role:shop'])->group(function () {
        // Gestion des produits
        Route::resource('products', ProductController::class)->except(['index', 'show', 'destroy']);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Gestion des commandes
        Route::prefix('shop/orders')->group(function () {
            Route::get('/', [OrderController::class, 'shopIndex'])->name('shop.orders.index');
            Route::get('/{order}', [OrderController::class, 'shopShow'])->name('shop.orders.show');
            Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('shop.orders.update-status');
        });

        // Statistiques et rapports
        Route::prefix('shop/analytics')->group(function () {
            Route::get('/', [ShopController::class, 'analytics'])->name('shop.analytics');
            Route::get('/sales', [ShopController::class, 'sales'])->name('shop.analytics.sales');
            Route::get('/products', [ShopController::class, 'productAnalytics'])->name('shop.analytics.products');
        });
    });

    // =============================================
    // Routes pour les promoteurs/Demarcheur/Ambassadeur
    // =============================================
    Route::middleware(['role:promoter'])->group(function () {
        // Gestion des publicités
        Route::resource('advertisements', AdvertisementController::class);
        Route::post('advertisements/{advertisement}/toggle-status', [AdvertisementController::class, 'toggleStatus'])->name('advertisements.toggle-status');
    });

    // =============================================
    // Routes pour les administrateurs
    // =============================================
    Route::middleware(['role:admin'])->group(function () {
        // Gestion des utilisateurs
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Gestion des boutiques
        Route::post('shops/{shop}/approve', [ShopController::class, 'approve'])->name('shops.approve');
        Route::post('shops/{shop}/reject', [ShopController::class, 'reject'])->name('shops.reject');
        Route::delete('shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');
        Route::get('shops/pending', [ShopController::class, 'pending'])->name('shops.pending');

        // Gestion des catégories
        Route::resource('categories', CategoryController::class)->except(['show', 'destroy']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Gestion des abonnements
        Route::resource('subscriptions', SubscriptionController::class);
        Route::post('subscriptions/{subscription}/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle-status');

        // Tableau de bord administrateur
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
            Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
            Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        });
    });

    // Routes pour les promotions
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    Route::post('/promotions/{promotion}/toggle', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle');
    Route::post('/promotions/validate', [PromotionController::class, 'validateCode'])->name('promotions.validate');
});

// Routes pour les paiements
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/{order}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/orders/{order}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/orders/{order}/verify', [PaymentController::class, 'verify'])->name('payment.verify');
    Route::post('/orders/{order}/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::delete('/orders/{order}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/notifications', [NotificationsController::class, 'index'])->name('profile.notifications');
    Route::put('/profile/notifications/{id}', [NotificationsController::class, 'markAsRead'])->name('profile.notifications.read');
    Route::delete('/profile/notifications/{id}', [NotificationsController::class, 'destroy'])->name('profile.notifications.delete');
});

require __DIR__ . '/auth.php';
