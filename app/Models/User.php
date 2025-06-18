<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasAdminFeatures;
use App\Models\Traits\HasCustomerFeatures;
use App\Models\Traits\HasPromoterFeatures;
use App\Models\Traits\HasShopFeatures;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Billable;
    use HasCustomerFeatures, HasShopFeatures, HasPromoterFeatures, HasAdminFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'avatar',
        'is_active',
        'settings',
        'preferences',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'settings' => 'array',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isShop(): bool
    {
        return $this->role === 'shop';
    }

    public function isPromoter(): bool
    {
        return $this->role === 'promoter';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function getRoleName(): string
    {
        return match ($this->role) {
            'admin' => 'Administrateur',
            'shop' => 'Boutique',
            'promoter' => 'Démarcheur',
            'customer' => 'Client',
            default => 'Utilisateur'
        };
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
