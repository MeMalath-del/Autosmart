<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QaVote extends Model
{
    protected $fillable = ['user_id', 'voteable_type', 'voteable_id', 'is_helpful'];

    protected $casts = ['is_helpful' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voteable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::created(function ($vote) {
            if ($vote->is_helpful) {
                $vote->voteable->increment('helpful_count');
            }
        });
    }
}
