<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partnership extends Model
{
   use HasFactory, SoftDeletes;

   protected $fillable = [
      'promoter_id',
      'shop_id',
      'code',
      'commission_rate',
      'status',
      'start_date',
      'end_date',
      'terms',
      'settings',
   ];

   protected $casts = [
      'commission_rate' => 'decimal:2',
      'start_date' => 'datetime',
      'end_date' => 'datetime',
      'terms' => 'array',
      'settings' => 'array',
   ];

   public function promoter(): BelongsTo
   {
      return $this->belongsTo(User::class, 'promoter_id');
   }

   public function shop(): BelongsTo
   {
      return $this->belongsTo(Shop::class);
   }

   public function isActive(): bool
   {
      return $this->status === 'active' &&
         $this->start_date->isPast() &&
         (!$this->end_date || $this->end_date->isFuture());
   }

   public function isExpired(): bool
   {
      return $this->end_date && $this->end_date->isPast();
   }

   public function isSuspended(): bool
   {
      return $this->status === 'suspended';
   }

   public function isTerminated(): bool
   {
      return $this->status === 'terminated';
   }
}
