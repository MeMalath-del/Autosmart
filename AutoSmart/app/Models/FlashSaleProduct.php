<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleProduct extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'sale_price',
        'quantity_limit',
        'sold_count',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
    ];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isAvailable(): bool
    {
        if (! $this->flashSale->isActive()) {
            return false;
        }
        if ($this->quantity_limit && $this->sold_count >= $this->quantity_limit) {
            return false;
        }

        return true;
    }

    public function getRemainingQuantityAttribute(): ?int
    {
        if (! $this->quantity_limit) {
            return null;
        }

        return max(0, $this->quantity_limit - $this->sold_count);
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (! $this->product || $this->product->price <= 0) {
            return 0;
        }

        return round((($this->product->price - $this->sale_price) / $this->product->price) * 100);
    }
}
