<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
   use HasFactory;

   protected $fillable = [
      'product_id',
      'path',
      'order',
      'is_default'
   ];

   protected $casts = [
      'order' => 'integer',
      'is_default' => 'boolean'
   ];

   public function product(): BelongsTo
   {
      return $this->belongsTo(Product::class);
   }

   
}
