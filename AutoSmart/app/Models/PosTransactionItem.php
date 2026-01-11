<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'discount', 'total'];
    protected $casts = ['unit_price' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2'];

    public function transaction(): BelongsTo { return $this->belongsTo(PosTransaction::class, 'transaction_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
