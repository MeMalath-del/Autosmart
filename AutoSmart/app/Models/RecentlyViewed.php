<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentlyViewed extends Model
{
    protected $table = 'recently_viewed';
    protected $fillable = ['user_id', 'session_id', 'product_id'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function record(int $productId): void
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        self::where('product_id', $productId)
            ->where(fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->delete();

        self::create(['product_id' => $productId, 'user_id' => $userId, 'session_id' => $sessionId]);

        // Keep only last 20 items
        $query = self::where(fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId));
        if ($query->count() > 20) {
            $query->orderBy('created_at')->limit($query->count() - 20)->delete();
        }
    }

    public static function getRecent(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::with('product.images')
            ->where(fn($q) => $q->where('user_id', auth()->id())->orWhere('session_id', session()->getId()))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->pluck('product');
    }
}
