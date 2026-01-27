<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchLog extends Model
{
    protected $fillable = ['query', 'user_id', 'session_id', 'results_count', 'has_click'];

    protected $casts = ['has_click' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $query, int $resultsCount): void
    {
        self::create([
            'query' => $query,
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'results_count' => $resultsCount,
        ]);
    }

    public static function getPopularSearches(int $limit = 10): \Illuminate\Support\Collection
    {
        return self::select('query')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'query');
    }
}
