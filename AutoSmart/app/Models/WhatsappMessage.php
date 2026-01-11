<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'user_id', 'phone_number', 'direction', 'type', 'content',
        'template_name', 'template_params', 'message_id', 'status', 'error_message'
    ];

    protected $casts = ['template_params' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeOutgoing($query) { return $query->where('direction', 'outgoing'); }
    public function scopeIncoming($query) { return $query->where('direction', 'incoming'); }

    public function markDelivered(): void { $this->update(['status' => 'delivered']); }
    public function markRead(): void { $this->update(['status' => 'read']); }
}
