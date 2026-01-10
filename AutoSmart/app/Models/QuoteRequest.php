<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    protected $fillable = ['business_account_id', 'request_number', 'requirements', 'needed_by', 'status', 'expires_at'];
    protected $casts = ['needed_by' => 'date', 'expires_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($qr) => $qr->request_number = $qr->request_number ?? 'RFQ-' . strtoupper(uniqid()));
    }

    public function businessAccount(): BelongsTo { return $this->belongsTo(BusinessAccount::class); }
    public function items(): HasMany { return $this->hasMany(QuoteRequestItem::class); }
    public function quotes(): HasMany { return $this->hasMany(SellerQuote::class); }

    public function isOpen(): bool { return $this->status === 'open' && (!$this->expires_at || $this->expires_at->isFuture()); }
}
