<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preorder extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'quantity', 'deposit_amount', 'deposit_paid',
        'expected_date', 'status', 'notify_when_available'
    ];

    protected $casts = [
        'deposit_amount' => 'decimal:2', 'deposit_paid' => 'boolean',
        'expected_date' => 'date', 'notify_when_available' => 'boolean'
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopePending($query) { return $query->where('status', 'pending'); }

    public function confirm(): void { $this->update(['status' => 'confirmed']); }
    public function markReady(): void { $this->update(['status' => 'ready']); }
    public function cancel(): void { $this->update(['status' => 'cancelled']); }
}
