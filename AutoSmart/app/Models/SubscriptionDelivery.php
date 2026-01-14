<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionDelivery extends Model
{
    protected $fillable = ['subscription_id', 'order_id', 'scheduled_date', 'status', 'notes'];

    protected $casts = ['scheduled_date' => 'date'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
