<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;

class Webhook extends Model
{
    protected $fillable = ['store_id', 'name', 'url', 'secret', 'events', 'is_active', 'failure_count', 'last_triggered_at'];

    protected $casts = ['events' => 'array', 'is_active' => 'boolean', 'last_triggered_at' => 'datetime'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    public function trigger(string $event, array $payload): bool
    {
        if (! $this->is_active || ! in_array($event, $this->events)) {
            return false;
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'X-Webhook-Secret' => $this->secret,
                'X-Webhook-Event' => $event,
            ])->post($this->url, $payload);

            $this->logs()->create([
                'event' => $event, 'payload' => $payload, 'response_status' => $response->status(),
                'response_body' => $response->body(), 'is_successful' => $response->successful(),
            ]);

            $this->update(['last_triggered_at' => now(), 'failure_count' => 0]);

            return $response->successful();
        } catch (\Exception $e) {
            $this->logs()->create(['event' => $event, 'payload' => $payload, 'error_message' => $e->getMessage()]);
            $this->increment('failure_count');

            return false;
        }
    }
}
