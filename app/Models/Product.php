<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'shop_id',
      'category_id',
      'name',
      'slug',
      'description',
      'price',
      'sale_price',
      'stock',
      'sku',
      'default_image',
      'is_featured',
      'is_active',
      'attributes',
      'variations',
      'average_rating',
      'reviews_count',
      'sales_count',
      'views_count'
   ];

   protected $casts = [
      'price' => 'decimal:2',
      'sale_price' => 'decimal:2',
      'stock' => 'integer',
      'is_featured' => 'boolean',
      'is_active' => 'boolean',
      'attributes' => 'array',
      'variations' => 'array',
      'average_rating' => 'decimal:2',
      'reviews_count' => 'integer',
      'sales_count' => 'integer',
      'views_count' => 'integer'
   ];

   public function shop(): BelongsTo
   {
      return $this->belongsTo(Shop::class);
   }

   public function category(): BelongsTo
   {
      return $this->belongsTo(Category::class);
   }

   public function orderItems(): HasMany
   {
      return $this->hasMany(OrderItem::class);
   }

   public function productReviews(): HasMany
   {
      return $this->hasMany(ProductReview::class);
   }

   public function images(): HasMany
   {
      return $this->hasMany(ProductImage::class);
   }

   public function getFinalPriceAttribute(): float
   {
      return $this->sale_price ?? $this->price;
   }

   public function getDiscountPercentageAttribute(): ?int
   {
      if (!$this->sale_price) {
         return null;
      }

      return (int) (100 - ($this->sale_price / $this->price * 100));
   }

   public function getMainImageAttribute(): ?string
   {
      if ($this->default_image) {
         return $this->default_image;
      }

      $defaultImage = $this->images()->where('is_default', true)->first();
      if ($defaultImage) {
         return $defaultImage->path;
      }

      $firstImage = $this->images()->first();
      return $firstImage ? $firstImage->path : null;
   }

   public function getIsOnSaleAttribute(): bool
   {
      return $this->sale_price !== null && $this->sale_price < $this->price;
   }

   public function getIsOutOfStockAttribute(): bool
   {
      return $this->stock <= 0;
   }

   public function getIsLowStockAttribute(): bool
   {
      return $this->stock > 0 && $this->stock <= 5;
   }

   public function scopeActive($query)
   {
      return $query->where('is_active', true);
   }

   public function scopeFeatured($query)
   {
      return $query->where('is_featured', true);
   }

   public function scopeOnSale($query)
   {
      return $query->whereNotNull('sale_price')
         ->whereColumn('sale_price', '<', 'price');
   }

   public function scopeInStock($query)
   {
      return $query->where('stock', '>', 0);
   }

   public function scopeByCategory($query, $categoryId)
   {
      return $query->where('category_id', $categoryId);
   }

   public function scopeByShop($query, $shopId)
   {
      return $query->where('shop_id', $shopId);
   }

   public function scopeSearch($query, $search)
   {
      return $query->where(function ($query) use ($search) {
         $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%");
      });
   }

   public function updateMetrics(): void
   {
      $this->reviews_count = $this->productReviews()->count();
      $this->average_rating = $this->productReviews()->avg('rating') ?? 0;
      $this->sales_count = $this->orderItems()->sum('quantity');
      $this->save();
   }

   public function incrementViews(): void
   {
      $this->increment('views_count');
   }

   public static function getAllUsedAlgorithm(?int $limit = null)
   {
      $data = self::active()
         ->inStock()
         ->with(['shop', 'category', 'images'])
         ->get()
         ->map(function ($product) {
            // Calcul du score composite pour chaque produit
            $composite_score = 0;

            // Note moyenne pondérée (40%)
            $average_rating = is_numeric($product->average_rating) ? (float) $product->average_rating : 0;
            $composite_score += 0.4 * $average_rating;

            // Nombre d'avis pondéré (20%)
            $composite_score += 0.2 * log($product->reviews_count + 1);

            // Nombre de ventes pondéré (25%)
            $composite_score += 0.25 * log($product->sales_count + 1);

            // Nombre de vues pondéré (15%)
            $composite_score += 0.15 * log($product->views_count + 1);

            // Bonus de nouveauté (+0.5 si créé dans les 30 derniers jours)
            if ($product->created_at->diffInDays(Carbon::now()) <= 30) {
               $composite_score += 0.5;
            }

            // Bonus de promotion
            if ($product->sale_price) {
               $composite_score += (($product->price - $product->sale_price) / $product->price);
            }

            // Formatage des données pour le frontend
            $defaultImage = $product->images()->where('is_default', true)->first();

            return [
               'id' => $product->id,
               'name' => $product->name,
               'slug' => $product->slug,
               'default_image' => $defaultImage ? $defaultImage->path : $product->images()->first()?->path,
               'images' => $product->images()->get()->map(function ($image) {
                  return [
                     'id' => $image->id,
                     'path' => $image->path,
                     'is_default' => $image->is_default,
                     'order' => $image->order
                  ];
               })->values(),
               'price' => $product->price,
               'sale_price' => $product->sale_price,
               'formatted_price' => number_format($product->price, 2, ',', ' ') . ' €',
               'formatted_sale_price' => $product->sale_price ? number_format($product->sale_price, 2, ',', ' ') . ' €' : null,
               'average_rating' => $average_rating,
               'shop' => [
                  'name' => $product->shop->name,
                  'is_verified' => $product->shop->is_verified
               ],
               'isOnSale' => !is_null($product->sale_price),
               'discount_percentage' => $product->sale_price ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0,
               'stock' => $product->stock,
               'composite_score' => round($composite_score, 2)
            ];
         })
         ->sortByDesc('composite_score');

      if ($limit) {
         $data = $data->take($limit);
      }

      return $data->values();
   }

   public function getCompositeScoreAttribute(): float
   {
      $score = 0;

      // Score de base (40%)
      $score += 0.4 * $this->average_rating;

      // Score de popularité (30%)
      $popularityScore = 0;
      $popularityScore += 0.15 * log($this->reviews_count + 1);
      $popularityScore += 0.15 * log($this->sales_count + 1);
      $score += 0.3 * $popularityScore;

      // Score de fraîcheur (20%)
      $daysSinceCreation = $this->created_at->diffInDays(now());
      $freshnessScore = max(0, 1 - ($daysSinceCreation / 365));
      $score += 0.2 * $freshnessScore;

      // Score de disponibilité (10%)
      $availabilityScore = min(1, $this->stock / 10);
      $score += 0.1 * $availabilityScore;

      // Bonus pour les produits en promotion
      if ($this->isOnSale) {
         $discountPercentage = $this->discount_percentage / 100;
         $score += 0.2 * $discountPercentage;
      }

      return round($score, 2);
   }

   public function scopeTrending($query)
   {
      return $query->where('is_active', true)
         ->where('stock', '>', 0)
         ->orderByRaw('(average_rating * 0.4 + LOG(reviews_count + 1) * 0.3 + LOG(sales_count + 1) * 0.3) DESC');
   }

   public function scopeNewArrivals($query)
   {
      return $query->where('is_active', true)
         ->where('stock', '>', 0)
         ->latest()
         ->take(8);
   }

   public function scopeBestSellers($query)
   {
      return $query->where('is_active', true)
         ->where('stock', '>', 0)
         ->orderByDesc('sales_count')
         ->take(8);
   }

   public function scopeSeasonalDeals($query)
   {
      return $query->where('is_active', true)
         ->where('stock', '>', 0)
         ->whereNotNull('sale_price')
         ->whereColumn('sale_price', '<', 'price')
         ->orderByRaw('((price - sale_price) / price) DESC')
         ->take(8);
   }

   public function scopeFlashDeals($query)
   {
      return $query->where('is_active', true)
         ->where('stock', '>', 0)
         ->whereNotNull('sale_price')
         ->whereColumn('sale_price', '<', 'price')
         ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)')
         ->orderByRaw('((price - sale_price) / price) DESC')
         ->take(4);
   }

   public function getFormattedPriceAttribute(): string
   {
      return number_format($this->price, 2, ',', ' ') . ' €';
   }

   public function getFormattedSalePriceAttribute(): ?string
   {
      return $this->sale_price ? number_format($this->sale_price, 2, ',', ' ') . ' €' : null;
   }

   public function getStockStatusAttribute(): array
   {
      if ($this->stock <= 0) {
         return ['text' => 'Rupture de stock', 'color' => 'text-red-500'];
      }
      if ($this->stock <= 5) {
         return ['text' => 'Plus que ' . $this->stock . ' en stock', 'color' => 'text-orange-500'];
      }
      return ['text' => 'En stock', 'color' => 'text-green-500'];
   }
}
