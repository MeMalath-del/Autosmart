<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopReview extends Model
{
    protected $fillable = [
        'workshop_id', 'user_id', 'maintenance_booking_id', 'rating',
        'service_rating', 'price_rating', 'time_rating', 'comment', 'is_approved'
    ];

    protected $casts = ['is_approved' => 'boolean'];

    public function workshop(): BelongsTo { return $this->belongsTo(Workshop::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function booking(): BelongsTo { return $this->belongsTo(MaintenanceBooking::class, 'maintenance_booking_id'); }

    protected static function booted()
    {
        static::saved(fn($review) => $review->workshop->updateRating());
        static::deleted(fn($review) => $review->workshop->updateRating());
    }
}
