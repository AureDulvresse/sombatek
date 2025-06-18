<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'is_public',
        'description',
        'max_items',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'max_items' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function addItem(Product $product, array $options = []): ?WishlistItem
    {
        // Vérifier la limite d'articles
        if ($this->max_items && $this->items()->count() >= $this->max_items) {
            return null;
        }

        // Vérifier si le produit existe déjà
        if ($this->items()->where('product_id', $product->id)->exists()) {
            return null;
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->sale_price ?? $product->price,
            'default_image' => $product->default_image,
            'options' => $options,
        ]);
    }

    public function removeItem(int $productId): bool
    {
        return $this->items()->where('product_id', $productId)->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public function getItemCount(): int
    {
        return $this->items()->count();
    }

    public function isFull(): bool
    {
        return $this->max_items && $this->getItemCount() >= $this->max_items;
    }

    public function share(): string
    {
        if (!$this->is_public) {
            return '';
        }

        return route('wishlist.shared', [
            'id' => $this->id,
            'token' => $this->generateShareToken()
        ]);
    }

    protected function generateShareToken(): string
    {
        return hash_hmac('sha256', $this->id . $this->user_id, config('app.key'));
    }

    public function verifyShareToken(string $token): bool
    {
        return hash_equals($this->generateShareToken(), $token);
    }

    public function moveToCart(Cart $cart): void
    {
        foreach ($this->items as $item) {
            $cart->addItem($item->product, 1, $item->options);
        }
    }

    public function duplicate(?string $newName = null): self
    {
        $duplicate = $this->replicate();
        $duplicate->name = $newName ?? $this->name . ' (copie)';
        $duplicate->save();

        foreach ($this->items as $item) {
            $duplicate->items()->create([
                'product_id' => $item->product_id,
                'options' => $item->options,
            ]);
        }

        return $duplicate;
    }
}
