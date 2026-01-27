<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id', 'warehouse_id', 'type', 'quantity', 'quantity_before',
        'quantity_after', 'reference_type', 'reference_id', 'notes', 'user_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in' => 'إدخال',
            'out' => 'إخراج',
            'adjustment' => 'تعديل',
            'transfer' => 'نقل',
            default => $this->type
        };
    }
}
