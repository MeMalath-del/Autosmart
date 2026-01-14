<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePayout extends Model
{
    protected $fillable = ['affiliate_id', 'amount', 'payment_method', 'payment_details', 'status', 'transaction_id', 'notes', 'processed_at'];

    protected $casts = ['amount' => 'decimal:2', 'payment_details' => 'array', 'processed_at' => 'datetime'];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }
}
