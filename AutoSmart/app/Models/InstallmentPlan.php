<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    protected $fillable = [
        'name', 'provider', 'months', 'min_amount', 'max_amount', 'interest_rate', 'is_active'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function calculateMonthlyPayment(float $amount): float
    {
        $totalWithInterest = $amount * (1 + $this->interest_rate / 100);
        return round($totalWithInterest / $this->months, 2);
    }

    public function isApplicable(float $amount): bool
    {
        if ($amount < $this->min_amount) return false;
        if ($this->max_amount && $amount > $this->max_amount) return false;
        return true;
    }
}
