<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'return_number', 'type', 'reason', 'description',
        'images', 'status', 'refund_amount', 'refund_method', 'approved_by',
        'approved_at', 'return_shipping_label', 'return_tracking_number',
        'received_at', 'inspection_notes', 'refunded_at',
    ];

    protected $casts = [
        'images' => 'array', 'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime', 'received_at' => 'datetime', 'refunded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($r) => $r->return_number = $r->return_number ?? 'RET-'.strtoupper(uniqid()));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function approve(User $approver, float $refundAmount): void
    {
        $this->update(['status' => 'approved', 'approved_by' => $approver->id, 'approved_at' => now(), 'refund_amount' => $refundAmount]);
    }

    public function markReceived(): void
    {
        $this->update(['status' => 'received', 'received_at' => now()]);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed', 'refunded_at' => now()]);
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'defective' => 'المنتج معيب', 'wrong_item' => 'منتج خاطئ',
            'not_as_described' => 'لا يطابق الوصف', 'changed_mind' => 'غيرت رأيي', default => 'أخرى'
        };
    }
}
