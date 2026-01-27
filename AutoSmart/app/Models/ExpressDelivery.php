<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpressDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'zone_id',
        'type',
        'fee',
        'promised_delivery_time',
        'actual_delivery_time',
        'status',
        'driver_id',
    ];

    protected $casts = [
        'fee' => 'float',
        'promised_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function zone()
    {
        return $this->belongsTo(ExpressDeliveryZone::class, 'zone_id');
    }

    public function driver()
    {
        return $this->belongsTo(DeliveryDriver::class, 'driver_id');
    }

    public function markDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'actual_delivery_time' => now(),
        ]);
    }

    public function isOnTime()
    {
        if (! $this->actual_delivery_time) {
            return null;
        }

        return $this->actual_delivery_time <= $this->promised_delivery_time;
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'assigned' => '<span class="badge bg-info">تم التعيين</span>',
            'picked_up' => '<span class="badge bg-primary">تم الاستلام</span>',
            'in_transit' => '<span class="badge bg-info">في الطريق</span>',
            'delivered' => '<span class="badge bg-success">تم التوصيل</span>',
            'failed' => '<span class="badge bg-danger">فشل</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
