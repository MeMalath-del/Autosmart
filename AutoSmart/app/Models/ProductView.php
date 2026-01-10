<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductView extends Model
{
    protected $fillable = ['product_id', 'user_id', 'session_id', 'ip_address', 'user_agent', 'referrer'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function record(int $productId, ?int $userId = null): void
    {
        self::create([
            'product_id' => $productId,
            'user_id' => $userId ?? auth()->id(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer')
        ]);
    }
}
