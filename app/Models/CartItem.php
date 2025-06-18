<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'name',
        'default_image',
        'options'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
        'options' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartItem) {
            $cartItem->subtotal = $cartItem->price * $cartItem->quantity;
        });

        static::updating(function ($cartItem) {
            $cartItem->subtotal = $cartItem->price * $cartItem->quantity;
        });
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotal(): float
    {
        return $this->quantity * $this->price;
    }

    public function updateQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->delete();
        }

        $this->quantity = $quantity;
        return $this->save();
    }

    public function updateSubtotal(): void
    {
        $this->subtotal = $this->price * $this->quantity;
        $this->save();
    }
}
