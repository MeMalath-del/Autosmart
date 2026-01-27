<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessAccount extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'company_name_ar', 'tax_number', 'commercial_register',
        'business_type', 'address', 'city', 'contact_person', 'contact_phone',
        'credit_limit', 'current_balance', 'payment_terms_days', 'status', 'is_verified'
    ];

    protected $casts = ['credit_limit' => 'decimal:2', 'current_balance' => 'decimal:2', 'is_verified' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function quoteRequests(): HasMany { return $this->hasMany(QuoteRequest::class); }
    public function creditInvoices(): HasMany { return $this->hasMany(CreditInvoice::class); }

    public function scopeApproved($query) { return $query->where('status', 'approved'); }

    public function hasAvailableCredit(float $amount): bool
    {
        return $this->current_balance + $amount <= $this->credit_limit;
    }

    public function getAvailableCreditAttribute(): float { return $this->credit_limit - $this->current_balance; }

    public function getBusinessTypeLabelAttribute(): string
    {
        return match($this->business_type) {
            'workshop' => 'ورشة', 'dealer' => 'وكيل', 'wholesaler' => 'تاجر جملة',
            'retailer' => 'تاجر تجزئة', default => 'أخرى'
        };
    }
}
