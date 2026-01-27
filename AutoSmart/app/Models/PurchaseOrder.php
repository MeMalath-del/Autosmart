<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'store_id', 'supplier_id', 'warehouse_id', 'order_number', 'status',
        'subtotal', 'tax', 'total', 'expected_date', 'received_date', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2',
        'expected_date' => 'date', 'received_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($po) => $po->order_number = $po->order_number ?? 'PO-'.strtoupper(uniqid()));
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $this->tax = $this->subtotal * 0.15;
        $this->total = $this->subtotal + $this->tax;
        $this->save();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة', 'sent' => 'مرسل', 'confirmed' => 'مؤكد',
            'received' => 'مستلم', 'cancelled' => 'ملغي', default => $this->status
        };
    }
}
