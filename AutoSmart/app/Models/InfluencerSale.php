<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerSale extends Model
{
    protected $fillable = ['influencer_id', 'order_id', 'order_total', 'commission', 'status'];
    protected $casts = ['order_total' => 'decimal:2', 'commission' => 'decimal:2'];

    public function influencer(): BelongsTo { return $this->belongsTo(Influencer::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
