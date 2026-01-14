<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOffer extends Model
{
    protected $fillable = ['user_id', 'offer_id', 'is_claimed', 'is_used', 'order_id', 'claimed_at', 'used_at', 'expires_at'];

    protected $casts = ['is_claimed' => 'boolean', 'is_used' => 'boolean', 'claimed_at' => 'datetime', 'used_at' => 'datetime', 'expires_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(TargetedOffer::class, 'offer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isValid(): bool
    {
        return ! $this->is_used && $this->expires_at->isFuture();
    }
}
