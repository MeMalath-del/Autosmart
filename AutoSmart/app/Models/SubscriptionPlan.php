<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'name_ar', 'description', 'billing_cycle', 'price', 'discount_percentage',
        'features', 'included_products', 'is_active', 'is_featured', 'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2', 'discount_percentage' => 'decimal:2',
        'features' => 'array', 'included_products' => 'array',
        'is_active' => 'boolean', 'is_featured' => 'boolean'
    ];

    public function subscriptions(): HasMany { return $this->hasMany(UserSubscription::class, 'plan_id'); }

    public function scopeActive($query) { return $query->where('is_active', true)->orderBy('sort_order'); }

    public function getLocalizedNameAttribute(): string { return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name; }

    public function getCycleLabelAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'شهري', 'quarterly' => 'ربع سنوي', 'yearly' => 'سنوي', default => $this->billing_cycle
        };
    }
}
