<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'currency',
        'is_active',
        'last_transaction_at',
    ];

    protected $casts = [
        'balance' => 'float',
        'pending_balance' => 'float',
        'is_active' => 'boolean',
        'last_transaction_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function topups()
    {
        return $this->hasMany(WalletTopup::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(WalletWithdrawal::class);
    }

    public function credit($amount, $description, $reference = null, $referenceType = null)
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);

        return $this->transactions()->create([
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $reference,
        ]);
    }

    public function debit($amount, $description, $reference = null, $referenceType = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);

        return $this->transactions()->create([
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $reference,
        ]);
    }

    public function refund($amount, $orderId)
    {
        return $this->credit($amount, "استرداد للطلب #{$orderId}", $orderId, Order::class);
    }

    public function addCashback($amount, $orderId)
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);

        return $this->transactions()->create([
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            'type' => 'cashback',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => "كاش باك للطلب #{$orderId}",
            'reference_type' => Order::class,
            'reference_id' => $orderId,
        ]);
    }

    public function canAfford($amount)
    {
        return $this->balance >= $amount;
    }

    public function getTotalBalanceAttribute()
    {
        return $this->balance + $this->pending_balance;
    }
}
