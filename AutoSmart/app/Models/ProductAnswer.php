<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProductAnswer extends Model
{
    protected $fillable = ['question_id', 'user_id', 'answer', 'is_seller_answer', 'is_best_answer', 'helpful_count', 'is_approved'];

    protected $casts = ['is_seller_answer' => 'boolean', 'is_best_answer' => 'boolean', 'is_approved' => 'boolean'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProductQuestion::class, 'question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(QaVote::class, 'voteable');
    }

    public function markAsBest(): void
    {
        $this->question->answers()->update(['is_best_answer' => false]);
        $this->update(['is_best_answer' => true]);
        $this->question->markAsAnswered();
    }
}
