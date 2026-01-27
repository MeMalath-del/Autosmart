<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'reason', 'refund_type', 'amount', 'status',
        'admin_notes', 'bank_name', 'account_number', 'iban', 'processed_at'
    ];

    protected $casts = ['amount' => 'decimal:2', 'processed_at' => 'datetime'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(string $reason): void
    {
        $this->update(['status' => 'rejected', 'admin_notes' => $reason]);
    }

    public function process(): void
    {
        if ($this->refund_type === 'wallet') {
            $wallet = Wallet::getOrCreateForUser($this->user_id);
            $wallet->credit($this->amount, 'استرداد طلب #' . $this->order->order_number, 'refund', $this->id);
        }
        $this->update(['status' => 'processed', 'processed_at' => now()]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'processed' => 'تم التنفيذ',
            default => $this->status
        };
    }
}
