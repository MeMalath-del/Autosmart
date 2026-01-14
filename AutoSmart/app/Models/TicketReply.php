<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'message', 'attachments', 'is_internal', 'is_from_customer'];

    protected $casts = ['attachments' => 'array', 'is_internal' => 'boolean', 'is_from_customer' => 'boolean'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
