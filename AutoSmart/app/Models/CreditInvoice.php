<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditInvoice extends Model
{
    protected $fillable = ['business_account_id', 'order_id', 'invoice_number', 'amount', 'due_date', 'paid_amount', 'status'];

    protected $casts = ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'due_date' => 'date'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($inv) => $inv->invoice_number = $inv->invoice_number ?? 'INV-'.strtoupper(uniqid()));
    }

    public function businessAccount(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getRemainingAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'paid';
    }
}
