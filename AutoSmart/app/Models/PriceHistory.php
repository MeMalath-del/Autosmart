<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    protected $table = 'price_history';
    protected $fillable = ['product_id', 'old_price', 'new_price', 'reason', 'rule_id', 'changed_by'];
    protected $casts = ['old_price' => 'decimal:2', 'new_price' => 'decimal:2'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function rule(): BelongsTo { return $this->belongsTo(DynamicPricingRule::class, 'rule_id'); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); }

    public function getChangePercentageAttribute(): float { return $this->old_price > 0 ? (($this->new_price - $this->old_price) / $this->old_price) * 100 : 0; }
}
