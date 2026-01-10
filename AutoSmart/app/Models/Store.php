<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'name_ar',
        'slug',
        'description',
        'logo',
        'banner',
        'phone',
        'whatsapp',
        'email',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'status',
        'rating',
        'rating_count',
        'is_verified',
        'is_featured',
        'approved_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(StoreReview::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(PartRequestQuote::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function updateRating(): void
    {
        $this->rating = $this->reviews()->avg('rating') ?? 0;
        $this->rating_count = $this->reviews()->count();
        $this->save();
    }
}
