<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'months',
        'interest_rate',
        'min_amount',
        'max_amount',
        'down_payment_percentage',
        'is_active',
    ];

    protected $casts = [
        'interest_rate' => 'float',
        'min_amount' => 'float',
        'max_amount' => 'float',
        'down_payment_percentage' => 'float',
        'is_active' => 'boolean',
    ];

    public function requests()
    {
        return $this->hasMany(InstallmentRequest::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAmount($query, $amount)
    {
        return $query->where('min_amount', '<=', $amount)
                     ->where('max_amount', '>=', $amount);
    }

    public function calculateMonthlyPayment($amount)
    {
        $downPayment = $amount * ($this->down_payment_percentage / 100);
        $financedAmount = $amount - $downPayment;
        
        if ($this->interest_rate == 0) {
            return $financedAmount / $this->months;
        }
        
        $monthlyRate = $this->interest_rate / 100 / 12;
        return ($financedAmount * $monthlyRate * pow(1 + $monthlyRate, $this->months)) / 
               (pow(1 + $monthlyRate, $this->months) - 1);
    }

    public function calculateTotalWithInterest($amount)
    {
        $downPayment = $amount * ($this->down_payment_percentage / 100);
        return $downPayment + ($this->calculateMonthlyPayment($amount) * $this->months);
    }

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }
}
