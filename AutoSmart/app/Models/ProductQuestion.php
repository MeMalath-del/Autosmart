<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProductQuestion extends Model
{
    protected $fillable = ['product_id', 'user_id', 'question', 'is_answered', 'is_approved', 'helpful_count'];

    protected $casts = ['is_answered' => 'boolean', 'is_approved' => 'boolean'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ProductAnswer::class, 'question_id');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(QaVote::class, 'voteable');
    }

    public function getBestAnswerAttribute(): ?ProductAnswer
    {
        return $this->answers()->where('is_best_answer', true)->first()
            ?? $this->answers()->orderByDesc('helpful_count')->first();
    }

    public function markAsAnswered(): void
    {
        $this->update(['is_answered' => true]);
    }
}
