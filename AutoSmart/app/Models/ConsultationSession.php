<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSession extends Model
{
    protected $fillable = [
        'user_id', 'expert_id', 'session_number', 'type', 'topic', 'description',
        'duration_minutes', 'price', 'scheduled_at', 'status', 'notes',
        'rating', 'review', 'meeting_link'
    ];

    protected $casts = ['price' => 'decimal:2', 'scheduled_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($s) => $s->session_number = $s->session_number ?? 'CON-' . strtoupper(uniqid()));
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function expert(): BelongsTo { return $this->belongsTo(User::class, 'expert_id'); }

    public function confirm(): void { $this->update(['status' => 'confirmed']); }
    public function complete(): void { $this->update(['status' => 'completed']); }
}
