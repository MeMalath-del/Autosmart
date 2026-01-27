<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'user_car_id', 'status', 'starts_at', 'ends_at',
        'next_billing_at', 'cancelled_at', 'cancellation_reason', 'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'datetime', 'ends_at' => 'datetime', 'next_billing_at' => 'datetime',
        'cancelled_at' => 'datetime', 'auto_renew' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function userCar(): BelongsTo
    {
        return $this->belongsTo(UserCar::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(SubscriptionDelivery::class, 'subscription_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (! $this->ends_at || $this->ends_at->isFuture());
    }

    public function cancel(?string $reason = null): void
    {
        $this->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancellation_reason' => $reason, 'auto_renew' => false]);
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }
}
