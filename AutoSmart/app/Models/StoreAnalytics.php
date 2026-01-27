<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAnalytics extends Model
{
    protected $fillable = ['store_id', 'date', 'views', 'unique_visitors', 'product_views', 'add_to_cart', 'orders', 'revenue'];

    protected $casts = ['date' => 'date', 'revenue' => 'decimal:2'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public static function increment(int $storeId, string $field, float $value = 1): void
    {
        $analytics = self::firstOrCreate(['store_id' => $storeId, 'date' => today()]);
        $analytics->increment($field, $value);
    }

    public function getConversionRateAttribute(): float
    {
        return $this->views > 0 ? round(($this->orders / $this->views) * 100, 2) : 0;
    }
}
