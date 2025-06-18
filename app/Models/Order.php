<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'user_id',
      'shop_id',
      'promoter_id',
      'order_number',
      'subtotal',
      'tax',
      'shipping',
      'total',
      'commission_amount',
      'promoter_commission',
      'status',
      'payment_status',
      'payment_method',
      'shipping_address',
      'billing_address',
      'tracking_number',
      'notes',
   ];

   protected $casts = [
      'subtotal' => 'decimal:2',
      'tax' => 'decimal:2',
      'shipping' => 'decimal:2',
      'total' => 'decimal:2',
      'commission_amount' => 'decimal:2',
      'promoter_commission' => 'decimal:2',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }

   public function shop(): BelongsTo
   {
      return $this->belongsTo(Shop::class);
   }

   public function promoter(): BelongsTo
   {
      return $this->belongsTo(User::class, 'promoter_id');
   }

   public function items(): HasMany
   {
      return $this->hasMany(OrderItem::class);
   }

   public function commission(): HasOne
   {
      return $this->hasOne(Commission::class);
   }

   public function isPending(): bool
   {
      return $this->status === 'pending';
   }

   public function isProcessing(): bool
   {
      return $this->status === 'processing';
   }

   public function isShipped(): bool
   {
      return $this->status === 'shipped';
   }

   public function isDelivered(): bool
   {
      return $this->status === 'delivered';
   }

   public function isCancelled(): bool
   {
      return $this->status === 'cancelled';
   }

   public function isPaid(): bool
   {
      return $this->payment_status === 'paid';
   }

   public function isRefunded(): bool
   {
      return $this->payment_status === 'refunded';
   }

   public function scopePending($query)
   {
      return $query->where('status', 'pending');
   }

   public function scopeProcessing($query)
   {
      return $query->where('status', 'processing');
   }

   public function scopeShipped($query)
   {
      return $query->where('status', 'shipped');
   }

   public function scopeDelivered($query)
   {
      return $query->where('status', 'delivered');
   }

   public function scopeCancelled($query)
   {
      return $query->where('status', 'cancelled');
   }

   public function scopePaid($query)
   {
      return $query->where('payment_status', 'paid');
   }

   public function scopeRefunded($query)
   {
      return $query->where('payment_status', 'refunded');
   }

   public function scopeByShop($query, $shopId)
   {
      return $query->where('shop_id', $shopId);
   }

   public function scopeByUser($query, $userId)
   {
      return $query->where('user_id', $userId);
   }

   public function scopeByPromoter($query, $promoterId)
   {
      return $query->where('promoter_id', $promoterId);
   }
}
