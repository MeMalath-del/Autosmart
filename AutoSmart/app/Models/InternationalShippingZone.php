<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'country_name',
        'country_name_ar',
        'zone',
        'base_rate',
        'per_kg_rate',
        'estimated_days_min',
        'estimated_days_max',
        'restricted_items',
        'is_active',
    ];

    protected $casts = [
        'base_rate' => 'float',
        'per_kg_rate' => 'float',
        'restricted_items' => 'array',
        'is_active' => 'boolean',
    ];

    public function shipments()
    {
        return $this->hasMany(InternationalShipment::class, 'zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateShippingCost($weight)
    {
        return $this->base_rate + ($weight * $this->per_kg_rate);
    }

    public function getEstimatedDeliveryAttribute()
    {
        return "{$this->estimated_days_min}-{$this->estimated_days_max} أيام";
    }

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' 
            ? ($this->country_name_ar ?? $this->country_name) 
            : $this->country_name;
    }
}
