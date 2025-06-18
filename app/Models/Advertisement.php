<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'shop_id',
      'title',
      'description',
      'image',
      'link',
      'position',
      'start_date',
      'end_date',
      'price',
      'status',
      'clicks',
      'impressions',
   ];

   protected $casts = [
      'start_date' => 'datetime',
      'end_date' => 'datetime',
      'price' => 'decimal:2',
      'clicks' => 'integer',
      'impressions' => 'integer',
   ];

   public function shop(): BelongsTo
   {
      return $this->belongsTo(Shop::class);
   }

   public function isActive(): bool
   {
      return $this->status === 'active' &&
         $this->start_date->isPast() &&
         $this->end_date->isFuture();
   }

   public function isExpired(): bool
   {
      return $this->end_date->isPast();
   }

   public function incrementClicks(): void
   {
      $this->increment('clicks');
   }

   public function incrementImpressions(): void
   {
      $this->increment('impressions');
   }
}
