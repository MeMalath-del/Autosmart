<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->orderByDesc('created_at');
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function credit(float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null, ?array $meta = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $referenceType, $referenceId, $meta) {
            $this->increment('balance', $amount);
            
            return $this->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $this->fresh()->balance,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'meta' => $meta,
            ]);
        });
    }

    public function debit(float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null, ?array $meta = null): WalletTransaction
    {
        if ($amount > $this->balance) {
            throw new \Exception('رصيد المحفظة غير كافي');
        }

        return DB::transaction(function () use ($amount, $description, $referenceType, $referenceId, $meta) {
            $this->decrement('balance', $amount);
            
            return $this->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $this->fresh()->balance,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'meta' => $meta,
            ]);
        });
    }

    public function canWithdraw(float $amount): bool
    {
        return $this->is_active && $this->balance >= $amount;
    }

    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(['user_id' => $userId]);
    }
}
