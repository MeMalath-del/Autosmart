<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyClaim extends Model
{
    protected $fillable = [
        'claim_number',
        'user_id',
        'order_id',
        'order_item_id',
        'product_id',
        'store_id',
        'issue_description',
        'images',
        'status',
        'store_response',
        'admin_notes',
        'resolution',
        'resolved_at',
    ];

    protected $casts = [
        'images' => 'array',
        'resolved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($claim) {
            $claim->claim_number = 'WC-' . strtoupper(uniqid());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'قيد الانتظار',
            'under_review' => 'تحت المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'in_repair' => 'قيد الإصلاح',
            'replaced' => 'تم الاستبدال',
            'refunded' => 'تم الاسترداد',
            'closed' => 'مغلق',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'in_repair' => 'primary',
            'replaced' => 'success',
            'refunded' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }

    public function getResolutionLabelAttribute(): ?string
    {
        return match ($this->resolution) {
            'repair' => 'إصلاح',
            'replace' => 'استبدال',
            'refund' => 'استرداد',
            default => null,
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
