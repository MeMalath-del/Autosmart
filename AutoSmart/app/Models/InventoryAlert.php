<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAlert extends Model
{
    protected $fillable = ['product_id', 'store_id', 'type', 'threshold', 'current_quantity', 'predicted_days_left', 'suggested_reorder_qty', 'is_resolved', 'resolved_at'];
    protected $casts = ['is_resolved' => 'boolean', 'resolved_at' => 'datetime'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }

    public function scopeUnresolved($query) { return $query->where('is_resolved', false); }
    public function scopeCritical($query) { return $query->where('predicted_days_left', '<=', 3); }
}
