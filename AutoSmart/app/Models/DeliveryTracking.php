<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryTracking extends Model
{
    protected $fillable = ['order_id', 'driver_id', 'latitude', 'longitude', 'status', 'notes', 'estimated_minutes'];
    protected $casts = ['latitude' => 'decimal:8', 'longitude' => 'decimal:8'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function driver(): BelongsTo { return $this->belongsTo(DeliveryDriver::class, 'driver_id'); }
}
