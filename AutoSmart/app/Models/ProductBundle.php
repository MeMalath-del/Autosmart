<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBundle extends Model
{
    protected $fillable = [
        'store_id', 'name', 'name_ar', 'description', 'image', 'regular_price',
        'bundle_price', 'quantity', 'is_active', 'is_featured', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2', 'bundle_price' => 'decimal:2',
        'is_active' => 'boolean', 'is_featured' => 'boolean',
        'starts_at' => 'datetime', 'ends_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BundleItem::class, 'bundle_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_items', 'bundle_id', 'product_id')->withPivot('quantity');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }

    public function getSavingsPercentageAttribute(): int
    {
        return $this->regular_price > 0 ? round((($this->regular_price - $this->bundle_price) / $this->regular_price) * 100) : 0;
    }
}
