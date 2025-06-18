<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'name',
      'stripe_id',
      'stripe_status',
      'stripe_price',
      'quantity',
      'trial_ends_at',
      'ends_at',
      'type',
      'amount',
      'features',
   ];

   protected $casts = [
      'quantity' => 'integer',
      'trial_ends_at' => 'datetime',
      'ends_at' => 'datetime',
      'amount' => 'decimal:2',
      'features' => 'array',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }

   public function items(): HasMany
   {
      return $this->hasMany(SubscriptionItem::class);
   }

   public function isActive(): bool
   {
      return $this->stripe_status === 'active';
   }

   public function isOnTrial(): bool
   {
      return $this->trial_ends_at && $this->trial_ends_at->isFuture();
   }

   public function isCancelled(): bool
   {
      return $this->ends_at && $this->ends_at->isPast();
   }
}
