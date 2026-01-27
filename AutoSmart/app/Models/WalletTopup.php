<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTopup extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'payment_method',
        'payment_reference',
        'fee',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'fee' => 'float',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function complete($reference = null)
    {
        $this->update([
            'status' => 'completed',
            'payment_reference' => $reference,
        ]);
        
        $this->wallet->credit($this->amount, 'شحن المحفظة', $this->id, self::class);
    }

    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fee;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'completed' => '<span class="badge bg-success">مكتمل</span>',
            'failed' => '<span class="badge bg-danger">فشل</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
