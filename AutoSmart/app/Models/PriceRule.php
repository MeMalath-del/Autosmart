<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRule extends Model
{
    protected $fillable = [
        'store_id', 'product_id', 'category_id', 'name', 'type', 'min_quantity',
        'discount_type', 'discount_value', 'customer_group', 'starts_at', 'ends_at', 'is_active', 'priority'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2', 'is_active' => 'boolean',
        'starts_at' => 'datetime', 'ends_at' => 'datetime'
    ];

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }

    public function isApplicable(int $quantity = 1, ?string $customerGroup = null): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->ends_at && $this->ends_at->isPast()) return false;
        if ($this->min_quantity && $quantity < $this->min_quantity) return false;
        if ($this->customer_group && $customerGroup !== $this->customer_group) return false;
        return true;
    }

    public function calculateDiscount(float $price): float
    {
        return $this->discount_type === 'percentage' 
            ? $price * ($this->discount_value / 100) 
            : min($this->discount_value, $price);
    }
}
