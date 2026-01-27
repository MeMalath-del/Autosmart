<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartInsurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'coverage',
        'duration_months',
        'price_percentage',
        'min_price',
        'max_coverage',
        'is_active',
    ];

    protected $casts = [
        'price_percentage' => 'float',
        'min_price' => 'float',
        'max_coverage' => 'float',
        'is_active' => 'boolean',
    ];

    public function policies()
    {
        return $this->hasMany(InsurancePolicy::class, 'insurance_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculatePremium($productPrice)
    {
        $premium = $productPrice * ($this->price_percentage / 100);
        return max($premium, $this->min_price);
    }
}
