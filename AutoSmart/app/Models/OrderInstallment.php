<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderInstallment extends Model
{
    protected $fillable = [
        'order_id', 'installment_plan_id', 'total_amount', 'monthly_amount',
        'total_months', 'paid_months', 'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function plan(): BelongsTo { return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id'); }

    public function getRemainingMonthsAttribute(): int
    {
        return $this->total_months - $this->paid_months;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->monthly_amount * $this->remaining_months;
    }
}
