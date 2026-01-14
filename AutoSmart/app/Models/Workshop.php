<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Workshop extends Model
{
    protected $fillable = [
        'user_id', 'name', 'name_ar', 'slug', 'description', 'logo', 'banner',
        'phone', 'email', 'whatsapp', 'address', 'city', 'latitude', 'longitude',
        'working_hours', 'specialties', 'car_brands', 'status', 'rating',
        'reviews_count', 'is_verified', 'is_featured',
    ];

    protected $casts = [
        'working_hours' => 'array', 'specialties' => 'array', 'car_brands' => 'array',
        'rating' => 'decimal:2', 'is_verified' => 'boolean', 'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($w) => $w->slug = $w->slug ?? Str::slug($w->name));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(WorkshopService::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(MaintenanceQuote::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(MaintenanceBooking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(WorkshopReview::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function updateRating(): void
    {
        $this->update([
            'rating' => $this->reviews()->avg('rating') ?? 0,
            'reviews_count' => $this->reviews()->count(),
        ]);
    }
}
