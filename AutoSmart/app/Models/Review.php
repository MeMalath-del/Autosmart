<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'quality_rating',
        'price_rating',
        'shipping_rating',
        'comment',
        'images',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
        'unhelpful_count',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'images' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function response(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReviewResponse::class);
    }

    public function votes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function markHelpful(int $userId): void
    {
        $vote = $this->votes()->updateOrCreate(
            ['user_id' => $userId],
            ['is_helpful' => true]
        );
        $this->updateVoteCounts();
    }

    public function markUnhelpful(int $userId): void
    {
        $vote = $this->votes()->updateOrCreate(
            ['user_id' => $userId],
            ['is_helpful' => false]
        );
        $this->updateVoteCounts();
    }

    protected function updateVoteCounts(): void
    {
        $this->update([
            'helpful_count' => $this->votes()->where('is_helpful', true)->count(),
            'unhelpful_count' => $this->votes()->where('is_helpful', false)->count(),
        ]);
    }
}
