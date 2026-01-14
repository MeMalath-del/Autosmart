<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSession extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'opening_balance', 'closing_balance', 'expected_balance',
        'difference', 'transactions_count', 'total_sales', 'total_refunds',
        'opened_at', 'closed_at', 'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2', 'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2', 'difference' => 'decimal:2',
        'total_sales' => 'decimal:2', 'total_refunds' => 'decimal:2',
        'opened_at' => 'datetime', 'closed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(StoreBranch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PosTransaction::class, 'session_id');
    }

    public function isOpen(): bool
    {
        return $this->closed_at === null;
    }

    public function close(float $closingBalance): void
    {
        $expected = $this->opening_balance + $this->total_sales - $this->total_refunds;
        $this->update([
            'closing_balance' => $closingBalance,
            'expected_balance' => $expected,
            'difference' => $closingBalance - $expected,
            'closed_at' => now(),
        ]);
    }
}
