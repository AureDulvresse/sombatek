<?php

namespace App\Models\Traits;

use App\Models\Commission;
use App\Models\Partnership;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasPromoterFeatures
{
    public function partnerships(): HasMany
    {
        return $this->hasMany(Partnership::class, 'promoter_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'promoter_id');
    }


    public function getActivePartnerships()
   {
      return $this->partnerships()
         ->where('status', 'active')
         ->get();
   }

   public function getTotalCommissions(): float
   {
      return $this->commissions()
         ->where('status', 'paid')
         ->sum('amount');
   }

   public function getPendingCommissions(): float
   {
      return $this->commissions()
         ->where('status', 'pending')
         ->sum('amount');
   }

   public function getPromotionStats()
   {
      return [
         'total_partnerships' => $this->partnerships()->count(),
         'active_partnerships' => $this->partnerships()->where('status', 'active')->count(),
         'total_commissions' => $this->getTotalCommissions(),
         'pending_commissions' => $this->getPendingCommissions(),
      ];
   }

   public function getRecentCommissions(int $limit = 5)
   {
      return $this->commissions()
         ->latest()
         ->take($limit)
         ->get();
   }
}
