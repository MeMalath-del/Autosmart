<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverLocation extends Model
{
    protected $fillable = ['driver_id', 'order_id', 'latitude', 'longitude', 'speed', 'heading'];
    protected $casts = ['latitude' => 'decimal:8', 'longitude' => 'decimal:8', 'speed' => 'decimal:2', 'heading' => 'decimal:2'];

    public function driver(): BelongsTo { return $this->belongsTo(DeliveryDriver::class, 'driver_id'); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
