<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipping_company_id',
        'tracking_number',
        'status',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(ShipmentTracking::class)->orderByDesc('tracked_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'في الانتظار',
            'picked_up' => 'تم الاستلام',
            'in_transit' => 'في الطريق',
            'out_for_delivery' => 'خارج للتوصيل',
            'delivered' => 'تم التوصيل',
            'failed' => 'فشل التوصيل',
            'returned' => 'مرتجع',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'picked_up' => 'info',
            'in_transit' => 'primary',
            'out_for_delivery' => 'info',
            'delivered' => 'success',
            'failed' => 'danger',
            'returned' => 'secondary',
            default => 'secondary',
        };
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->shippingCompany || !$this->tracking_number) return null;
        return $this->shippingCompany->getTrackingLink($this->tracking_number);
    }

    public function addTracking(string $status, ?string $location = null, ?string $description = null): ShipmentTracking
    {
        return $this->tracking()->create([
            'status' => $status,
            'location' => $location,
            'description' => $description,
            'tracked_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
        $this->addTracking('delivered', null, 'تم تسليم الشحنة بنجاح');
    }
}
