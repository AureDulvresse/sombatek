<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
   use HasFactory;

   protected $fillable = [
      'customer_id',
      'product_id',
      'rating',
      'comment'
   ];

   /**
    * Get the customer that wrote the review.
    */
   public function customer(): BelongsTo
   {
      return $this->belongsTo(User::class, 'customer_id');
   }

   /**
    * Get the product that was reviewed.
    */
   public function product(): BelongsTo
   {
      return $this->belongsTo(Product::class);
   }
}
