<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateSale extends Model
{
    protected $fillable = ['affiliate_id', 'order_id', 'click_id', 'order_total', 'commission', 'status', 'approved_at', 'paid_at'];

    protected $casts = ['order_total' => 'decimal:2', 'commission' => 'decimal:2', 'approved_at' => 'datetime', 'paid_at' => 'datetime'];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(AffiliateClick::class, 'click_id');
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved', 'approved_at' => now()]);
    }

    public function markPaid(): void
    {
        $this->update(['status' => 'paid', 'paid_at' => now()]);
    }
}
