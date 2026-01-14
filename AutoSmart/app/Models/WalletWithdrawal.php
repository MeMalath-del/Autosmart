<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'bank_name',
        'account_number',
        'iban',
        'account_holder_name',
        'fee',
        'status',
        'rejection_reason',
        'transaction_reference',
    ];

    protected $casts = [
        'amount' => 'float',
        'fee' => 'float',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function approve($reference = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_reference' => $reference,
        ]);
    }

    public function reject($reason)
    {
        // Return the amount to the wallet
        $this->wallet->credit($this->amount, 'إلغاء طلب سحب', $this->id, self::class);

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fee;
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'processing' => '<span class="badge bg-info">جاري المعالجة</span>',
            'completed' => '<span class="badge bg-success">مكتمل</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
