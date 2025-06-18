<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
   use HasFactory;

   protected $fillable = [
      'code',
      'description',
      'discount_type',
      'discount_value',
      'is_active',
      'expires_at',
      'min_order_amount',
      'max_discount',
      'usage_limit',
      'used_count',
   ];

   protected $casts = [
      'is_active' => 'boolean',
      'expires_at' => 'datetime',
      'min_order_amount' => 'decimal:2',
      'max_discount' => 'decimal:2',
      'discount_value' => 'decimal:2',
      'usage_limit' => 'integer',
      'used_count' => 'integer',
   ];

   public function isValid()
   {
      if (!$this->is_active) {
         return false;
      }

      if ($this->expires_at && $this->expires_at->isPast()) {
         return false;
      }

      if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
         return false;
      }

      return true;
   }

   public function calculateDiscount($orderAmount)
   {
      if ($orderAmount < $this->min_order_amount) {
         return 0;
      }

      $discount = $this->discount_type === 'percentage'
         ? ($orderAmount * $this->discount_value / 100)
         : $this->discount_value;

      if ($this->max_discount) {
         $discount = min($discount, $this->max_discount);
      }

      return $discount;
   }

   public function incrementUsage()
   {
      $this->increment('used_count');
   }
}
