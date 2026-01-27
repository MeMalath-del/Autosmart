<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandForecast extends Model
{
    protected $fillable = ['product_id', 'forecast_date', 'predicted_demand', 'actual_demand', 'confidence', 'factors'];

    protected $casts = ['forecast_date' => 'date', 'confidence' => 'decimal:4', 'factors' => 'array'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAccuracyAttribute(): ?float
    {
        if (! $this->actual_demand) {
            return null;
        }
        $diff = abs($this->predicted_demand - $this->actual_demand);

        return max(0, 100 - ($diff / max(1, $this->actual_demand)) * 100);
    }
}
