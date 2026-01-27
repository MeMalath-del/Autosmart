<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_car_id',
        'workshop_id',
        'order_id',
        'record_type',
        'service_date',
        'mileage_at_service',
        'description',
        'parts_used',
        'labor_cost',
        'parts_cost',
        'total_cost',
        'attachments',
        'next_service_mileage',
        'next_service_date',
        'technician_notes',
    ];

    protected $casts = [
        'service_date' => 'date',
        'parts_used' => 'array',
        'attachments' => 'array',
        'labor_cost' => 'float',
        'parts_cost' => 'float',
        'total_cost' => 'float',
        'next_service_date' => 'date',
    ];

    public function userCar()
    {
        return $this->belongsTo(UserCar::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getRecordTypeNameAttribute()
    {
        return match($this->record_type) {
            'oil_change' => 'تغيير زيت',
            'tire_rotation' => 'تدوير الإطارات',
            'brake_service' => 'صيانة الفرامل',
            'filter_change' => 'تغيير الفلاتر',
            'battery_service' => 'صيانة البطارية',
            'transmission' => 'صيانة ناقل الحركة',
            'engine' => 'صيانة المحرك',
            'ac_service' => 'صيانة التكييف',
            'suspension' => 'صيانة نظام التعليق',
            'general' => 'صيانة عامة',
            default => $this->record_type,
        };
    }

    public function isServiceDue()
    {
        if ($this->next_service_date && $this->next_service_date <= now()) {
            return true;
        }
        return false;
    }
}
