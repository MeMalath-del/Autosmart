<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedWarranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_item_id',
        'warranty_id',
        'price_paid',
        'original_warranty_end',
        'extended_warranty_end',
        'status',
    ];

    protected $casts = [
        'price_paid' => 'float',
        'original_warranty_end' => 'date',
        'extended_warranty_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function warranty()
    {
        return $this->belongsTo(ExtendedWarranty::class, 'warranty_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->extended_warranty_end->gt(now());
    }

    public function getRemainingDaysAttribute()
    {
        if (!$this->isActive()) return 0;
        return now()->diffInDays($this->extended_warranty_end);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success">نشط</span>',
            'expired' => '<span class="badge bg-secondary">منتهي</span>',
            'used' => '<span class="badge bg-warning">مستخدم</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
