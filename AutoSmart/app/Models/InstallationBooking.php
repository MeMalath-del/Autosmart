<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallationBooking extends Model
{
    protected $fillable = [
        'order_id', 'service_id', 'workshop_id', 'user_id', 'booking_number',
        'booking_date', 'booking_time', 'status', 'notes', 'user_car_details',
        'confirmed_at', 'completed_at',
    ];

    protected $casts = ['booking_date' => 'date', 'confirmed_at' => 'datetime', 'completed_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($b) => $b->booking_number = $b->booking_number ?? 'INS-'.strtoupper(uniqid()));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(InstallationService::class, 'service_id');
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
