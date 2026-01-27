<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'zone_id',
        'weight',
        'shipping_cost',
        'customs_fee',
        'customs_declaration_number',
        'tracking_number',
        'status',
    ];

    protected $casts = [
        'weight' => 'float',
        'shipping_cost' => 'float',
        'customs_fee' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function zone()
    {
        return $this->belongsTo(InternationalShippingZone::class, 'zone_id');
    }

    public function getTotalCostAttribute()
    {
        return $this->shipping_cost + $this->customs_fee;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'customs_processing' => '<span class="badge bg-info">في الجمارك</span>',
            'in_transit' => '<span class="badge bg-primary">في الطريق</span>',
            'delivered' => '<span class="badge bg-success">تم التوصيل</span>',
            'returned' => '<span class="badge bg-danger">مرتجع</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
