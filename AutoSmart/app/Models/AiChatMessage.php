<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'role',
        'content',
        'intent',
        'entities',
        'confidence',
        'suggestions',
        'helpful',
    ];

    protected $casts = [
        'intent' => 'array',
        'entities' => 'array',
        'suggestions' => 'array',
        'confidence' => 'float',
        'helpful' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }

    public function markHelpful($helpful = true)
    {
        $this->update(['helpful' => $helpful]);
    }

    public function isFromUser()
    {
        return $this->role === 'user';
    }

    public function isFromAssistant()
    {
        return $this->role === 'assistant';
    }
}
