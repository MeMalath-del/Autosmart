<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentTracking extends Model
{
    protected $table = 'shipment_tracking';

    protected $fillable = [
        'shipment_id',
        'status',
        'location',
        'description',
        'tracked_at',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
