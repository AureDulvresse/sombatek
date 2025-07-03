<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'user_id',
      'name',
      'slug',
      'logo',
      'description',
      'address',
      'phone',
      'email',
      'website',
      'social_media',
      'status',
      'is_verified',
      'commission_rate',
      'settings',
      'opening_hours',
      'delivery_options',
      'payment_methods',
      'minimum_order_amount',
      'delivery_radius',
   ];

   protected $casts = [
      'is_verified' => 'boolean',
      'commission_rate' => 'decimal:2',
      'settings' => 'array',
      'opening_hours' => 'array',
      'delivery_options' => 'array',
      'payment_methods' => 'array',
      'minimum_order_amount' => 'decimal:2',
      'delivery_radius' => 'integer',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }

   public function products(): HasMany
   {
      return $this->hasMany(Product::class);
   }

   public function orders(): HasMany
   {
      return $this->hasMany(Order::class);
   }

   public function partnerships(): HasMany
   {
      return $this->hasMany(Partnership::class);
   }

   public function commissions(): HasMany
   {
      return $this->hasMany(Commission::class);
   }

   public function advertisements(): HasMany
   {
      return $this->hasMany(Advertisement::class);
   }

   public function reviews(): HasMany
   {
      return $this->hasMany(Review::class);
   }

   public function isOpen(): bool
   {
      $now = now();
      $dayOfWeek = strtolower($now->format('l'));
      $currentTime = $now->format('H:i');

      $hours = $this->opening_hours[$dayOfWeek] ?? null;
      if (!$hours) return false;

      return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
   }

   public function acceptsDelivery(): bool
   {
      return in_array('delivery', $this->delivery_options);
   }

   public function acceptsPickup(): bool
   {
      return in_array('pickup', $this->delivery_options);
   }

   public function acceptsPaymentMethod(string $method): bool
   {
      return in_array($method, $this->payment_methods);
   }


   public function delivery_options(): array
   {
      return $this->delivery_options;
   }

   public static function getTopShops(?int $limit = null)
   {
      $data = self::with(['products' => function ($query) {
         $query->active()->inStock();
      }, 'reviews', 'orders'])
         ->get()
         ->map(function ($shop) {
            // Calcul du score composite pour chaque boutique
            $composite_score = 0;

            // Note moyenne des avis (30%)
            $avgRating = $shop->reviews->avg('rating') ?? 0;
            $composite_score += 0.3 * $avgRating;

            // Nombre d'avis (20%)
            $reviewsCount = $shop->reviews->count();
            $composite_score += 0.2 * log($reviewsCount + 1);

            // Nombre de produits en stock (15%)
            $productsCount = $shop->products->count();
            $composite_score += 0.15 * log($productsCount + 1);

            // Nombre de commandes (20%)
            $ordersCount = $shop->orders->count();
            $composite_score += 0.2 * log($ordersCount + 1);

            // Taux de commission (15%)
            $commissionScore = 1 - ($shop->commission_rate / 100);
            $composite_score += 0.15 * $commissionScore;

            // Bonus pour les boutiques vérifiées
            if ($shop->is_verified) {
               $composite_score += 0.5;
            }

            $shop->composite_score = round($composite_score, 2);
            return $shop;
         })
         ->sortByDesc('composite_score');

      if ($limit) {
         $data = $data->take($limit);
      }

      return $data->values();
   }

   public function getPerformanceScoreAttribute(): float
   {
      $score = 0;

      // Note moyenne (30%)
      $score += 0.3 * $this->rating;

      // Nombre d'avis (15%)
      $score += 0.15 * min(1, $this->review_count / 100);

      // Nombre de ventes (20%)
      $score += 0.2 * min(1, $this->total_sales / 1000);

      // Nombre de commandes (15%)
      $score += 0.15 * min(1, $this->total_orders / 500);

      // Nombre de produits (10%)
      $score += 0.1 * min(1, $this->total_products / 50);

      // Taux de commission (10%)
      $commissionScore = 1 - ($this->commission_rate / 100);
      $score += 0.1 * $commissionScore;

      // Bonus pour les boutiques vérifiées
      if ($this->is_verified) {
         $score += 0.2;
      }

      return round($score, 2);
   }

   public function scopeTopRated($query)
   {
      return $query->where('status', 'active')
         ->orderByDesc('rating')
         ->orderByDesc('review_count');
   }

   public function scopeMostPopular($query)
   {
      return $query->where('status', 'active')
         ->orderByDesc('total_sales')
         ->orderByDesc('total_orders');
   }

   public function scopeVerified($query)
   {
      return $query->where('is_verified', true)
         ->where('status', 'active');
   }

   public function getFormattedRatingAttribute(): string
   {
      return number_format($this->rating, 1);
   }

   public function getFormattedTotalSalesAttribute(): string
   {
      return number_format($this->total_sales);
   }

   public function getFormattedTotalOrdersAttribute(): string
   {
      return number_format($this->total_orders);
   }

   public function getFormattedTotalProductsAttribute(): string
   {
      return number_format($this->total_products);
   }

   public function getStatusBadgeAttribute(): array
   {
      return match ($this->status) {
         'active' => ['text' => 'Active', 'color' => 'text-green-500'],
         'pending' => ['text' => 'En attente', 'color' => 'text-yellow-500'],
         'suspended' => ['text' => 'Suspendue', 'color' => 'text-red-500'],
         default => ['text' => 'Inconnu', 'color' => 'text-gray-500'],
      };
   }

   public function getRouteKeyName()
   {
      return 'slug';
   }
}
