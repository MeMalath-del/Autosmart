<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'store_id', 'ticket_number', 'subject', 'category',
        'priority', 'status', 'assigned_to', 'first_response_at', 'resolved_at', 'satisfaction_rating',
    ];

    protected $casts = ['first_response_at' => 'datetime', 'resolved_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($t) => $t->ticket_number = $t->ticket_number ?? 'TKT-'.strtoupper(uniqid()));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
    }

    public function addReply(User $user, string $message, bool $isInternal = false): TicketReply
    {
        $reply = $this->replies()->create([
            'user_id' => $user->id, 'message' => $message,
            'is_internal' => $isInternal, 'is_from_customer' => $user->id === $this->user_id,
        ]);

        if (! $this->first_response_at && $user->id !== $this->user_id) {
            $this->update(['first_response_at' => now()]);
        }

        return $reply;
    }

    public function resolve(): void
    {
        $this->update(['status' => 'resolved', 'resolved_at' => now()]);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة', default => $this->priority
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'مفتوحة', 'in_progress' => 'جاري المعالجة', 'waiting_customer' => 'بانتظار العميل',
            'resolved' => 'محلولة', 'closed' => 'مغلقة', default => $this->status
        };
    }
}
