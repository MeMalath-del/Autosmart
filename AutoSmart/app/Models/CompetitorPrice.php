<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitorPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'competitor_name',
        'competitor_url',
        'price',
        'shipping_cost',
        'in_stock',
        'checked_at',
    ];

    protected $casts = [
        'price' => 'float',
        'shipping_cost' => 'float',
        'in_stock' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->price + ($this->shipping_cost ?? 0);
    }

    public function getPriceDifferenceAttribute()
    {
        if (!$this->product) return null;
        return $this->product->price - $this->price;
    }

    public function getPriceDifferencePercentAttribute()
    {
        if (!$this->product || $this->product->price == 0) return null;
        return round((($this->product->price - $this->price) / $this->price) * 100, 2);
    }

    public function isCheaper()
    {
        return $this->price < $this->product?->price;
    }
}
