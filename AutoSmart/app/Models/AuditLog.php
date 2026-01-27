<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'user_type', 'event', 'auditable_type', 'auditable_id',
        'old_values', 'new_values', 'ip_address', 'user_agent', 'url', 'tags'
    ];

    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'tags' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function auditable(): MorphTo { return $this->morphTo(); }

    public static function log(string $event, Model $model, array $oldValues = [], array $newValues = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }
}
