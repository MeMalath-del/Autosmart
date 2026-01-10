<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends Model
{
    protected $fillable = ['user_id', 'session_id', 'status', 'transferred_to'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function agent(): BelongsTo { return $this->belongsTo(User::class, 'transferred_to'); }
    public function messages(): HasMany { return $this->hasMany(ChatbotMessage::class, 'conversation_id'); }

    public function addMessage(string $message, string $sender = 'user', ?array $intent = null): ChatbotMessage
    {
        return $this->messages()->create([
            'message' => $message, 'sender' => $sender, 'intent' => $intent
        ]);
    }

    public function transferToAgent(User $agent): void
    {
        $this->update(['status' => 'transferred', 'transferred_to' => $agent->id]);
    }
}
