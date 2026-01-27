<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_orders',
        'email_promotions',
        'email_messages',
        'push_orders',
        'push_promotions',
        'push_messages',
    ];

    protected $casts = [
        'email_orders' => 'boolean',
        'email_promotions' => 'boolean',
        'email_messages' => 'boolean',
        'push_orders' => 'boolean',
        'push_promotions' => 'boolean',
        'push_messages' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
