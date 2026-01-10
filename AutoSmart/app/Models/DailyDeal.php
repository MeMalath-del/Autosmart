<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyDeal extends Model
{
    protected $fillable = [
        'product_id',
        'deal_price',
        'deal_date',
        'is_active',
    ];

    protected $casts = [
        'deal_price' => 'decimal:2',
        'deal_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeToday($query)
    {
        return $query->where('deal_date', today())
            ->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->product || $this->product->price <= 0) return 0;
        return round((($this->product->price - $this->deal_price) / $this->product->price) * 100);
    }
}
