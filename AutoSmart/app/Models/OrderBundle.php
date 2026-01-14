<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bundle_code',
        'original_shipping_total',
        'bundled_shipping_cost',
        'savings',
        'bundle_deadline',
        'status',
    ];

    protected $casts = [
        'original_shipping_total' => 'float',
        'bundled_shipping_cost' => 'float',
        'savings' => 'float',
        'bundle_deadline' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($bundle) {
            if (empty($bundle->bundle_code)) {
                $bundle->bundle_code = 'BND-'.strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bundledOrders()
    {
        return $this->hasMany(BundledOrder::class, 'bundle_id');
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, BundledOrder::class, 'bundle_id', 'id', 'id', 'order_id');
    }

    public function addOrder($order, $originalShipping)
    {
        $this->bundledOrders()->create([
            'order_id' => $order->id,
            'original_shipping' => $originalShipping,
            'allocated_shipping' => 0, // Will be calculated when bundle is closed
        ]);

        $this->increment('original_shipping_total', $originalShipping);
    }

    public function close()
    {
        $orderCount = $this->bundledOrders->count();
        if ($orderCount == 0) {
            return;
        }

        // Calculate bundled shipping cost (e.g., 50% discount for multiple orders)
        $discount = min(0.5, ($orderCount - 1) * 0.15);
        $this->bundled_shipping_cost = $this->original_shipping_total * (1 - $discount);
        $this->savings = $this->original_shipping_total - $this->bundled_shipping_cost;

        // Allocate shipping cost proportionally
        $allocatedPerOrder = $this->bundled_shipping_cost / $orderCount;
        $this->bundledOrders()->update(['allocated_shipping' => $allocatedPerOrder]);

        $this->status = 'closed';
        $this->save();
    }

    public function isOpen()
    {
        return $this->status === 'open' && $this->bundle_deadline->gt(now());
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'open' => '<span class="badge bg-success">مفتوح</span>',
            'closed' => '<span class="badge bg-warning">مغلق</span>',
            'shipped' => '<span class="badge bg-info">تم الشحن</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
