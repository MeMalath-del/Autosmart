<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartLifecycle extends Model
{
    protected $fillable = [
        'user_id', 'user_car_id', 'product_id', 'order_id', 'installation_date',
        'installation_mileage', 'expected_lifespan_km', 'expected_lifespan_months',
        'expected_replacement_date', 'expected_replacement_mileage',
        'actual_replacement_date', 'actual_replacement_mileage', 'status', 'notes'
    ];

    protected $casts = [
        'installation_date' => 'date', 'expected_replacement_date' => 'date', 'actual_replacement_date' => 'date'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function userCar(): BelongsTo { return $this->belongsTo(UserCar::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    public function isExpiring(): bool
    {
        if ($this->expected_replacement_date && $this->expected_replacement_date->diffInDays(now()) <= 30) return true;
        return false;
    }

    public function updateStatus(): void
    {
        if ($this->actual_replacement_date) { $this->update(['status' => 'replaced']); return; }
        if ($this->isExpiring()) { $this->update(['status' => 'warning']); return; }
        if ($this->expected_replacement_date?->isPast()) { $this->update(['status' => 'expired']); }
    }
}
