<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'amount',
        'bank_name',
        'account_number',
        'account_holder',
        'iban',
        'status',
        'admin_notes',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved', 'processed_at' => now()]);
    }

    public function reject(string $reason): void
    {
        $this->wallet->credit($this->amount, 'إرجاع طلب سحب مرفوض', 'withdrawal_request', $this->id);
        $this->update(['status' => 'rejected', 'admin_notes' => $reason, 'processed_at' => now()]);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed', 'processed_at' => now()]);
    }
}
