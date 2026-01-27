<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeInRequest extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'old_part_name', 'old_part_brand', 'old_part_condition',
        'old_part_images', 'estimated_value', 'offered_discount', 'status',
        'evaluation_notes', 'evaluated_by', 'order_id',
    ];

    protected $casts = ['old_part_images' => 'array', 'estimated_value' => 'decimal:2', 'offered_discount' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
