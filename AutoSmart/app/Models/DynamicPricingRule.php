<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicPricingRule extends Model
{
    protected $fillable = [
        'store_id', 'product_id', 'category_id', 'name', 'type', 'action',
        'value_type', 'value', 'min_price', 'max_price', 'conditions',
        'priority', 'is_active', 'starts_at', 'ends_at'
    ];

    protected $casts = [
        'value' => 'decimal:2', 'min_price' => 'decimal:2', 'max_price' => 'decimal:2',
        'conditions' => 'array', 'is_active' => 'boolean', 'starts_at' => 'datetime', 'ends_at' => 'datetime'
    ];

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }

    public function scopeActive($query) { return $query->where('is_active', true)->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now())); }

    public function calculatePrice(float $basePrice): float
    {
        $adjustment = $this->value_type === 'percentage' ? $basePrice * ($this->value / 100) : $this->value;
        $newPrice = match($this->action) {
            'increase' => $basePrice + $adjustment,
            'decrease' => $basePrice - $adjustment,
            'set' => $this->value,
            default => $basePrice
        };
        if ($this->min_price) $newPrice = max($newPrice, $this->min_price);
        if ($this->max_price) $newPrice = min($newPrice, $this->max_price);
        return round($newPrice, 2);
    }
}
