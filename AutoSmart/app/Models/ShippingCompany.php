<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCompany extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'logo',
        'tracking_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function getTrackingLink(string $trackingNumber): ?string
    {
        if (!$this->tracking_url) return null;
        return str_replace('{tracking_number}', $trackingNumber, $this->tracking_url);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
