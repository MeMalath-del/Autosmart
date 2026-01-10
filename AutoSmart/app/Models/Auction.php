<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    protected $fillable = [
        'product_id', 'store_id', 'title', 'description', 'starting_price', 'reserve_price',
        'buy_now_price', 'current_bid', 'bid_increment', 'bids_count', 'winner_id',
        'starts_at', 'ends_at', 'status', 'is_featured'
    ];

    protected $casts = [
        'starting_price' => 'decimal:2', 'reserve_price' => 'decimal:2', 'buy_now_price' => 'decimal:2',
        'current_bid' => 'decimal:2', 'bid_increment' => 'decimal:2', 'starts_at' => 'datetime',
        'ends_at' => 'datetime', 'is_featured' => 'boolean'
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function winner(): BelongsTo { return $this->belongsTo(User::class, 'winner_id'); }
    public function bids(): HasMany { return $this->hasMany(AuctionBid::class); }
    public function watchers(): HasMany { return $this->hasMany(AuctionWatcher::class); }

    public function scopeActive($query) { return $query->where('status', 'active')->where('ends_at', '>', now()); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }

    public function isActive(): bool { return $this->status === 'active' && $this->ends_at->isFuture(); }
    public function hasReservePrice(): bool { return $this->reserve_price !== null; }
    public function reserveMet(): bool { return !$this->hasReservePrice() || $this->current_bid >= $this->reserve_price; }

    public function getMinBidAttribute(): float
    {
        return $this->current_bid ? $this->current_bid + $this->bid_increment : $this->starting_price;
    }

    public function getTimeLeftAttribute(): string
    {
        if ($this->ends_at->isPast()) return 'انتهى';
        return $this->ends_at->diffForHumans();
    }

    public function placeBid(User $user, float $amount, ?float $maxAutoBid = null): ?AuctionBid
    {
        if (!$this->isActive() || $amount < $this->min_bid) return null;

        $this->bids()->where('is_winning', true)->update(['is_winning' => false]);

        $bid = $this->bids()->create([
            'user_id' => $user->id, 'amount' => $amount, 'max_auto_bid' => $maxAutoBid,
            'is_winning' => true, 'ip_address' => request()->ip()
        ]);

        $this->update(['current_bid' => $amount, 'bids_count' => $this->bids()->count()]);
        return $bid;
    }

    public function endAuction(): void
    {
        $winningBid = $this->bids()->where('is_winning', true)->first();
        $this->update([
            'status' => $winningBid && $this->reserveMet() ? 'sold' : 'ended',
            'winner_id' => $winningBid?->user_id
        ]);
    }
}
