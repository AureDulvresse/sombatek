<?php

namespace App\Policies;

use App\Models\ProductReview;
use App\Models\User;

class ProductReviewPolicy
{
   /**
    * Determine if the user can update the review.
    */
   public function update(User $user, ProductReview $review): bool
   {
      return $user->id === $review->customer_id;
   }

   /**
    * Determine if the user can delete the review.
    */
   public function delete(User $user, ProductReview $review): bool
   {
      return $user->id === $review->customer_id;
   }
}
