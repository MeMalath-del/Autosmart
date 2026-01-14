<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AiChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'status',
        'language',
        'context',
        'messages_count',
        'satisfaction_rating',
        'resolved',
        'closed_at',
    ];

    protected $casts = [
        'context' => 'array',
        'resolved' => 'boolean',
        'satisfaction_rating' => 'float',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'session_id');
    }

    public function addMessage($role, $content, $data = [])
    {
        $message = $this->messages()->create([
            'role' => $role,
            'content' => $content,
            'intent' => $data['intent'] ?? null,
            'entities' => $data['entities'] ?? null,
            'confidence' => $data['confidence'] ?? null,
            'suggestions' => $data['suggestions'] ?? null,
        ]);

        $this->increment('messages_count');

        return $message;
    }

    public function close($rating = null, $resolved = true)
    {
        $this->update([
            'status' => 'closed',
            'satisfaction_rating' => $rating,
            'resolved' => $resolved,
            'closed_at' => now(),
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
