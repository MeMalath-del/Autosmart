<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryDriver extends Model
{
    protected $fillable = [
        'user_id', 'store_id', 'name', 'phone', 'vehicle_type', 'vehicle_number',
        'license_number', 'latitude', 'longitude', 'status', 'rating',
        'deliveries_count', 'is_active', 'last_location_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8', 'longitude' => 'decimal:8', 'rating' => 'decimal:2',
        'is_active' => 'boolean', 'last_location_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(DeliveryTracking::class, 'driver_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(DriverLocation::class, 'driver_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function updateLocation(float $lat, float $lng, ?int $orderId = null): void
    {
        $this->update(['latitude' => $lat, 'longitude' => $lng, 'last_location_at' => now()]);
        $this->locations()->create(['latitude' => $lat, 'longitude' => $lng, 'order_id' => $orderId]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'متاح', 'busy' => 'مشغول', 'offline' => 'غير متصل', default => $this->status
        };
    }
}
