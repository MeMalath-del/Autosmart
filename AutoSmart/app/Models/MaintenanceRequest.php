<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'user_id', 'user_car_id', 'car_info', 'issue_description', 'images',
        'urgency', 'status', 'preferred_date', 'preferred_time', 'city'
    ];

    protected $casts = ['images' => 'array', 'preferred_date' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function userCar(): BelongsTo { return $this->belongsTo(UserCar::class); }
    public function quotes(): HasMany { return $this->hasMany(MaintenanceQuote::class); }

    public function scopeOpen($query) { return $query->where('status', 'open'); }

    public function getUrgencyLabelAttribute(): string
    {
        return match($this->urgency) { 'low' => 'عادي', 'medium' => 'متوسط', 'high' => 'عاجل', default => $this->urgency };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'مفتوح', 'quoted' => 'تم التسعير', 'booked' => 'محجوز',
            'in_progress' => 'جاري التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي', default => $this->status
        };
    }
}
