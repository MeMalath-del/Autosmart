<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AffiliateLink extends Model
{
    protected $fillable = ['affiliate_id', 'name', 'code', 'destination_url', 'product_id', 'category_id', 'clicks', 'conversions', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($l) => $l->code = $l->code ?? strtoupper(Str::random(10)));
    }

    public function affiliate(): BelongsTo { return $this->belongsTo(Affiliate::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
