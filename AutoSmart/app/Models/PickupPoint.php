<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    protected $fillable = ['name', 'name_ar', 'address', 'city', 'latitude', 'longitude', 'phone', 'working_hours', 'is_active'];

    protected $casts = ['latitude' => 'decimal:8', 'longitude' => 'decimal:8', 'working_hours' => 'array', 'is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }
}
