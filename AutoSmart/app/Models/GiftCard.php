<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $fillable = [
        'code', 'initial_balance', 'current_balance', 'purchased_by', 'recipient_id',
        'recipient_email', 'recipient_name', 'message', 'status', 'activated_at', 'expires_at',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2', 'current_balance' => 'decimal:2',
        'activated_at' => 'datetime', 'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($gc) => $gc->code = $gc->code ?? strtoupper(Str::random(16)));
    }

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && $this->current_balance > 0 &&
               (! $this->expires_at || $this->expires_at->isFuture());
    }

    public function activate(): void
    {
        $this->update(['status' => 'active', 'activated_at' => now()]);
    }

    public function redeem(float $amount, ?int $orderId = null, ?int $userId = null): bool
    {
        if (! $this->isValid() || $amount > $this->current_balance) {
            return false;
        }

        $this->transactions()->create([
            'type' => 'redeem', 'amount' => -$amount, 'order_id' => $orderId, 'user_id' => $userId,
            'balance_after' => $this->current_balance - $amount,
        ]);

        $this->decrement('current_balance', $amount);

        if ($this->current_balance <= 0) {
            $this->update(['status' => 'used']);
        }

        return true;
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', strtoupper($code))->first();
    }
}
