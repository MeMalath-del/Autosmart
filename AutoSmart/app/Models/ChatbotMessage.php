<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    protected $fillable = ['conversation_id', 'sender', 'message', 'intent', 'entities', 'confidence'];

    protected $casts = ['intent' => 'array', 'entities' => 'array', 'confidence' => 'decimal:4'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }
}
