<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartRequest extends Model
{
    protected $fillable = [
        'user_id',
        'car_brand_id',
        'car_model_id',
        'car_year',
        'part_name',
        'description',
        'part_number',
        'images',
        'urgency',
        'status',
        'budget_min',
        'budget_max',
        'expires_at',
    ];

    protected $casts = [
        'images' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(PartRequestQuote::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function getUrgencyLabelAttribute(): string
    {
        $labels = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
        ];

        return $labels[$this->urgency] ?? $this->urgency;
    }

    public function getUrgencyColorAttribute(): string
    {
        $colors = [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
        ];

        return $colors[$this->urgency] ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'open' => 'مفتوح',
            'quoted' => 'تم التسعير',
            'closed' => 'مغلق',
            'expired' => 'منتهي',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
