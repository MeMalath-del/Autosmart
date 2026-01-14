<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashbackRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_cashback',
        'applicable_categories',
        'applicable_stores',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'max_cashback' => 'float',
        'applicable_categories' => 'array',
        'applicable_stores' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function calculateCashback($orderAmount)
    {
        if ($orderAmount < $this->min_order_amount) {
            return 0;
        }

        $cashback = $this->type === 'percentage'
            ? $orderAmount * ($this->value / 100)
            : $this->value;

        if ($this->max_cashback) {
            $cashback = min($cashback, $this->max_cashback);
        }

        return round($cashback, 2);
    }

    public function appliesTo($order)
    {
        if (! empty($this->applicable_stores)) {
            // Check if order items from applicable stores
        }

        if (! empty($this->applicable_categories)) {
            // Check if order items from applicable categories
        }

        return true;
    }
}
