<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarMaintenanceLog extends Model
{
    protected $fillable = [
        'user_car_id', 'maintenance_date', 'type', 'mileage',
        'description', 'cost', 'service_provider',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function userCar(): BelongsTo
    {
        return $this->belongsTo(UserCar::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'oil_change' => 'تغيير زيت',
            'tire_rotation' => 'تدوير إطارات',
            'brake_service' => 'صيانة فرامل',
            'battery' => 'بطارية',
            'filter' => 'فلاتر',
            'general' => 'صيانة عامة',
            default => $this->type
        };
    }
}
