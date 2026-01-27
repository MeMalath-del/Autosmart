<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = ['webhook_id', 'event', 'payload', 'response_status', 'response_body', 'attempts', 'is_successful', 'error_message'];
    protected $casts = ['payload' => 'array', 'is_successful' => 'boolean'];

    public function webhook(): BelongsTo { return $this->belongsTo(Webhook::class); }
}
