<?php

namespace App\Models\Traits;

use App\Models\Advertisement;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Partnership;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasShopFeatures
{
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class, 'shop_id');
    }

    public function ambassadors(): HasMany
    {
        return $this->hasMany(Partnership::class, 'shop_id');
    }

    public function getShopOrders()
    {
        return Order::whereHas('items.product', function ($query) {
            $query->where('shop_id', $this->shop->id);
        })->latest()->get();
    }

    public function getActiveProducts()
    {
        return $this->products()
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->get();
    }

    public function getLowStockProducts(int $threshold = 5)
    {
        return $this->products()
            ->where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->get();
    }

    public function getOutOfStockProducts()
    {
        return $this->products()
            ->where('stock', 0)
            ->get();
    }

    public function getTotalSales(): float
    {
        return Order::whereHas('items.product', function ($query) {
            $query->where('shop_id', $this->shop->id);
        })->where('status', 'delivered')->sum('total');
    }

    public function getMonthlySales()
    {
        return Order::whereHas('items.product', function ($query) {
            $query->where('shop_id', $this->shop->id);
        })
            ->where('status', 'delivered')
            ->whereMonth('created_at', now()->month)
            ->sum('total');
    }

    public function getTopSellingProducts(int $limit = 5)
    {
        return $this->products()
            ->withCount(['orderItems as total_sales' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'delivered');
                });
            }])
            ->orderByDesc('total_sales')
            ->take($limit)
            ->get();
    }
}
