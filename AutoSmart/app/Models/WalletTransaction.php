<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function isCredit()
    {
        return in_array($this->type, ['credit', 'refund', 'cashback']);
    }

    public function isDebit()
    {
        return $this->type === 'debit';
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'credit' => 'إيداع',
            'debit' => 'سحب',
            'refund' => 'استرداد',
            'cashback' => 'كاش باك',
            'transfer' => 'تحويل',
            'adjustment' => 'تعديل',
            default => $this->type,
        };
    }

    public function getTypeBadgeAttribute()
    {
        return match ($this->type) {
            'credit' => '<span class="badge bg-success">إيداع</span>',
            'debit' => '<span class="badge bg-danger">سحب</span>',
            'refund' => '<span class="badge bg-info">استرداد</span>',
            'cashback' => '<span class="badge bg-warning">كاش باك</span>',
            'transfer' => '<span class="badge bg-primary">تحويل</span>',
            'adjustment' => '<span class="badge bg-secondary">تعديل</span>',
            default => '<span class="badge bg-secondary">'.$this->type.'</span>',
        };
    }
}
