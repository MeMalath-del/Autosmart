<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerQuote extends Model
{
    protected $fillable = ['quote_request_id', 'store_id', 'total_amount', 'delivery_days', 'notes', 'status', 'valid_until'];

    protected $casts = ['total_amount' => 'decimal:2', 'valid_until' => 'datetime'];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'pending' && (! $this->valid_until || $this->valid_until->isFuture());
    }
}
