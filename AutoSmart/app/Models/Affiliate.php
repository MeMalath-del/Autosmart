<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id', 'code', 'website', 'bio', 'commission_rate', 'total_earnings',
        'pending_earnings', 'paid_earnings', 'total_clicks', 'total_orders',
        'conversion_rate', 'status', 'payment_method', 'payment_details', 'minimum_payout',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2', 'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2', 'paid_earnings' => 'decimal:2',
        'conversion_rate' => 'decimal:2', 'payment_details' => 'array', 'minimum_payout' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($a) => $a->code = $a->code ?? strtoupper(Str::random(8)));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(AffiliateLink::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(AffiliateSale::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function canRequestPayout(): bool
    {
        return $this->pending_earnings >= $this->minimum_payout;
    }

    public function recordClick(?string $ip = null): AffiliateClick
    {
        $this->increment('total_clicks');

        return $this->clicks()->create(['ip_address' => $ip, 'user_agent' => request()->userAgent()]);
    }

    public function recordSale(Order $order, ?AffiliateClick $click = null): void
    {
        $commission = $order->total * ($this->commission_rate / 100);
        $this->sales()->create([
            'order_id' => $order->id, 'click_id' => $click?->id,
            'order_total' => $order->total, 'commission' => $commission,
        ]);
        $this->increment('total_orders');
        $this->increment('pending_earnings', $commission);
        $this->increment('total_earnings', $commission);
        $this->update(['conversion_rate' => ($this->total_orders / max(1, $this->total_clicks)) * 100]);
    }
}
