<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewSentiment extends Model
{
    protected $fillable = ['review_id', 'sentiment', 'confidence', 'keywords', 'aspects'];

    protected $casts = ['confidence' => 'decimal:4', 'keywords' => 'array', 'aspects' => 'array'];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function getSentimentLabelAttribute(): string
    {
        return match ($this->sentiment) {
            'positive' => 'إيجابي', 'negative' => 'سلبي', default => 'محايد'
        };
    }
}
