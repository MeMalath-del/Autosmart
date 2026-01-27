<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtendedWarranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'extra_months',
        'price_percentage',
        'coverage_details',
        'is_active',
    ];

    protected $casts = [
        'price_percentage' => 'float',
        'coverage_details' => 'array',
        'is_active' => 'boolean',
    ];

    public function purchasedWarranties()
    {
        return $this->hasMany(PurchasedWarranty::class, 'warranty_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculatePrice($productPrice)
    {
        return $productPrice * ($this->price_percentage / 100);
    }
}
