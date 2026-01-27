<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpressDeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'districts',
        'same_day_fee',
        'express_fee',
        'cutoff_time',
        'is_active',
    ];

    protected $casts = [
        'districts' => 'array',
        'same_day_fee' => 'float',
        'express_fee' => 'float',
        'cutoff_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function deliveries()
    {
        return $this->hasMany(ExpressDelivery::class, 'zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function canAcceptSameDayOrder()
    {
        return now()->format('H:i') < $this->cutoff_time;
    }

    public function getDeliveryFee($type)
    {
        return match ($type) {
            'same_day' => $this->same_day_fee,
            'express' => $this->express_fee,
            default => 0,
        };
    }
}
