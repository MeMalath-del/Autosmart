<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'referral_code',
        'referred_by',
        'phone',
        'avatar',
        'address',
        'city',
        'country',
        'is_active',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function partRequests(): HasMany
    {
        return $this->hasMany(PartRequest::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function warrantyClaims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function notificationSettings(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function getWalletBalance(): float
    {
        return $this->wallet?->balance ?? 0;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isWorkshop(): bool
    {
        return $this->role === 'workshop';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function hasStore(): bool
    {
        return $this->store()->exists();
    }

    public function hasApprovedStore(): bool
    {
        return $this->store && $this->store->isApproved();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=random';
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // ===============================
    // Phase 3 Relationships
    // ===============================

    public function cars(): HasMany
    {
        return $this->hasMany(UserCar::class);
    }

    public function productLists(): HasMany
    {
        return $this->hasMany(ProductList::class);
    }

    public function loyaltyPoints(): HasOne
    {
        return $this->hasOne(LoyaltyPoints::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function workshop(): HasOne
    {
        return $this->hasOne(Workshop::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function maintenanceBookings(): HasMany
    {
        return $this->hasMany(MaintenanceBooking::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function getLoyaltyPointsAttribute(): int
    {
        return $this->loyaltyPoints?->points ?? 0;
    }

    public function getPrimaryCar(): ?UserCar
    {
        return $this->cars()->where('is_primary', true)->first() ?? $this->cars()->first();
    }

    public function hasWorkshop(): bool
    {
        return $this->workshop()->exists();
    }

    public function hasApprovedWorkshop(): bool
    {
        return $this->workshop && $this->workshop->isApproved();
    }
}
