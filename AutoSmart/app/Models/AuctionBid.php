<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends Model
{
    protected $fillable = ['auction_id', 'user_id', 'amount', 'max_auto_bid', 'is_winning', 'is_auto_bid', 'ip_address'];
    protected $casts = ['amount' => 'decimal:2', 'max_auto_bid' => 'decimal:2', 'is_winning' => 'boolean', 'is_auto_bid' => 'boolean'];

    public function auction(): BelongsTo { return $this->belongsTo(Auction::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
