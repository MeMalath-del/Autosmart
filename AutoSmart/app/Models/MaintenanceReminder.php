<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReminder extends Model
{
    protected $fillable = [
        'user_id', 'user_car_id', 'type', 'interval_km', 'interval_months',
        'last_mileage', 'last_service_date', 'next_service_date', 'next_service_mileage',
        'is_active', 'notification_sent'
    ];

    protected $casts = [
        'last_service_date' => 'date', 'next_service_date' => 'date',
        'is_active' => 'boolean', 'notification_sent' => 'boolean'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function userCar(): BelongsTo { return $this->belongsTo(UserCar::class); }

    public function isDue(): bool
    {
        return $this->next_service_date && $this->next_service_date->isPast();
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'oil_change' => 'تغيير الزيت',
            'tire_rotation' => 'تبديل الإطارات',
            'brake_check' => 'فحص الفرامل',
            'filter_change' => 'تغيير الفلاتر',
            'battery_check' => 'فحص البطارية',
            default => $this->type
        };
    }
}
