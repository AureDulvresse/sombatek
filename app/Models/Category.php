<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'name',
      'slug',
      'description',
      'image',
      'parent_id',
      'order',
      'is_active',
      'attributes',
   ];

   protected $casts = [
      'order' => 'integer',
      'is_active' => 'boolean',
      'attributes' => 'array',
   ];

   public function parent(): BelongsTo
   {
      return $this->belongsTo(Category::class, 'parent_id');
   }

   public function children(): HasMany
   {
      return $this->hasMany(Category::class, 'parent_id');
   }

   public function products(): HasMany
   {
      return $this->hasMany(Product::class);
   }

   public function getFullPathAttribute(): string
   {
      $path = [$this->name];
      $parent = $this->parent;

      while ($parent) {
         array_unshift($path, $parent->name);
         $parent = $parent->parent;
      }

      return implode(' > ', $path);
   }

   public function getPopularityScoreAttribute(): float
   {
      $score = 0;

      // Nombre de produits (40%)
      $score += 0.4 * min(1, $this->products()->count() / 100);

      // Nombre de produits actifs (30%)
      $activeProducts = $this->products()->where('is_active', true)->count();
      $score += 0.3 * min(1, $activeProducts / 50);

      // Nombre de produits en stock (20%)
      $inStockProducts = $this->products()->where('stock', '>', 0)->count();
      $score += 0.2 * min(1, $inStockProducts / 30);

      // Nombre de produits en promotion (10%)
      $onSaleProducts = $this->products()->whereNotNull('sale_price')->count();
      $score += 0.1 * min(1, $onSaleProducts / 20);

      return round($score, 2);
   }

   public function scopePopular($query)
   {
      return $query->withCount(['products', 'products as active_products_count' => function ($query) {
         $query->where('is_active', true);
      }])
         ->orderByDesc('products_count')
         ->orderByDesc('active_products_count');
   }

   public function getProductsCountAttribute(): int
   {
      return $this->products()->where('is_active', true)->count();
   }

   public function getActiveProductsCountAttribute(): int
   {
      return $this->products()->where('is_active', true)->count();
   }

   public function getInStockProductsCountAttribute(): int
   {
      return $this->products()->where('stock', '>', 0)->count();
   }

   public function getOnSaleProductsCountAttribute(): int
   {
      return $this->products()->whereNotNull('sale_price')->count();
   }
}
