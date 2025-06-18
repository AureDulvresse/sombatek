<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'order_id',
      'promoter_id',
      'shop_id',
      'amount',
      'promoter_amount',
      'platform_amount',
      'status',
      'paid_at',
      'payment_reference',
      'notes',
   ];

   protected $casts = [
      'amount' => 'decimal:2',
      'promoter_amount' => 'decimal:2',
      'platform_amount' => 'decimal:2',
      'paid_at' => 'datetime',
   ];

   public function order(): BelongsTo
   {
      return $this->belongsTo(Order::class);
   }

   public function promoter(): BelongsTo
   {
      return $this->belongsTo(User::class, 'promoter_id');
   }

   public function shop(): BelongsTo
   {
      return $this->belongsTo(Shop::class);
   }

   public function isPaid(): bool
   {
      return $this->status === 'paid';
   }

   public function isPending(): bool
   {
      return $this->status === 'pending';
   }

   public function isCancelled(): bool
   {
      return $this->status === 'cancelled';
   }
}
