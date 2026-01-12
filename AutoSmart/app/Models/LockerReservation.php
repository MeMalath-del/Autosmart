<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockerReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'locker_id',
        'compartment_number',
        'compartment_size',
        'access_code',
        'reserved_until',
        'picked_up_at',
        'status',
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'picked_up_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function locker()
    {
        return $this->belongsTo(SmartLocker::class, 'locker_id');
    }

    public function markStored()
    {
        $this->update(['status' => 'stored']);
    }

    public function markPickedUp()
    {
        $this->update([
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);
        
        $this->locker->increment('available_compartments');
    }

    public function isExpired()
    {
        return $this->status === 'reserved' && $this->reserved_until->lt(now());
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'reserved' => '<span class="badge bg-warning">محجوز</span>',
            'stored' => '<span class="badge bg-info">مخزن</span>',
            'picked_up' => '<span class="badge bg-success">تم الاستلام</span>',
            'expired' => '<span class="badge bg-danger">منتهي</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
