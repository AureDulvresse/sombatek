<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionItem extends Model
{
   use HasFactory;

   protected $fillable = [
      'subscription_id',
      'stripe_id',
      'stripe_product',
      'stripe_price',
      'quantity',
   ];

   protected $casts = [
      'quantity' => 'integer',
   ];

   public function subscription(): BelongsTo
   {
      return $this->belongsTo(Subscription::class);
   }
}
