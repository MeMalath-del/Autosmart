<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCustomization extends Model
{
    protected $fillable = ['product_id', 'name', 'name_ar', 'type', 'options', 'extra_price', 'is_required', 'max_length', 'sort_order'];

    protected $casts = ['options' => 'array', 'extra_price' => 'decimal:2', 'is_required' => 'boolean'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }
}
