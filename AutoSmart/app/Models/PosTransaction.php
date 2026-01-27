<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTransaction extends Model
{
    protected $fillable = [
        'session_id', 'order_id', 'transaction_number', 'type', 'subtotal',
        'tax', 'discount', 'total', 'payment_method', 'amount_paid',
        'change_given', 'customer_id', 'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'discount' => 'decimal:2',
        'total' => 'decimal:2', 'amount_paid' => 'decimal:2', 'change_given' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($t) => $t->transaction_number = $t->transaction_number ?? 'POS-' . strtoupper(uniqid()));
    }

    public function session(): BelongsTo { return $this->belongsTo(PosSession::class, 'session_id'); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function items(): HasMany { return $this->hasMany(PosTransactionItem::class, 'transaction_id'); }
}
