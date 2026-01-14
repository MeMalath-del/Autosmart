<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\UserBehavior;
use Illuminate\Support\Collection;

class BehaviorAnalyticsService
{
    public function trackEvent(string $eventType, ?string $target = null, array $data = []): UserBehavior
    {
        return UserBehavior::track($eventType, $target, $data);
    }

    public function trackProductView(Product $product): void
    {
        $this->trackEvent('product_view', $product->id, [
            'product_name' => $product->name,
            'category_id' => $product->category_id,
            'price' => $product->price,
        ]);
    }

    public function trackSearch(string $query, int $resultsCount): void
    {
        $this->trackEvent('search', null, [
            'query' => $query,
            'results_count' => $resultsCount,
        ]);
    }

    public function trackAddToCart(Product $product, int $quantity): void
    {
        $this->trackEvent('add_to_cart', $product->id, [
            'product_name' => $product->name,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    public function trackPurchase(array $orderData): void
    {
        $this->trackEvent('purchase', $orderData['order_id'], [
            'total' => $orderData['total'],
            'items_count' => $orderData['items_count'],
        ]);
    }

    public function getUserInsights(User $user): array
    {
        $behaviors = UserBehavior::forUser($user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        return [
            'total_sessions' => $behaviors->groupBy('session_id')->count(),
            'total_events' => $behaviors->count(),
            'most_viewed_categories' => $this->getMostViewedCategories($behaviors),
            'search_history' => $this->getSearchHistory($behaviors),
            'purchase_likelihood' => $this->calculatePurchaseLikelihood($behaviors),
            'preferred_time' => $this->getPreferredBrowsingTime($behaviors),
            'device_breakdown' => $this->getDeviceBreakdown($behaviors),
        ];
    }

    protected function getMostViewedCategories(Collection $behaviors): array
    {
        $productViews = $behaviors->where('event_type', 'product_view');

        return $productViews->groupBy(function ($item) {
            return $item->event_data['category_id'] ?? 'unknown';
        })->map->count()->sortDesc()->take(5)->toArray();
    }

    protected function getSearchHistory(Collection $behaviors): array
    {
        return $behaviors->where('event_type', 'search')
            ->pluck('event_data.query')
            ->filter()
            ->unique()
            ->take(10)
            ->toArray();
    }

    protected function calculatePurchaseLikelihood(Collection $behaviors): float
    {
        $views = $behaviors->where('event_type', 'product_view')->count();
        $cartAdds = $behaviors->where('event_type', 'add_to_cart')->count();
        $purchases = $behaviors->where('event_type', 'purchase')->count();

        if ($views === 0) {
            return 0;
        }

        // Simple conversion funnel score
        $score = 0;
        $score += min(1, $views / 10) * 0.2;  // View activity
        $score += min(1, $cartAdds / 3) * 0.4; // Cart activity
        $score += min(1, $purchases) * 0.4;    // Purchase history

        return round($score, 2);
    }

    protected function getPreferredBrowsingTime(Collection $behaviors): ?string
    {
        if ($behaviors->isEmpty()) {
            return null;
        }

        $hourCounts = $behaviors->groupBy(function ($item) {
            return $item->created_at->format('H');
        })->map->count();

        $peakHour = $hourCounts->sortDesc()->keys()->first();

        return match (true) {
            $peakHour >= 6 && $peakHour < 12 => 'صباحاً',
            $peakHour >= 12 && $peakHour < 17 => 'ظهراً',
            $peakHour >= 17 && $peakHour < 21 => 'مساءً',
            default => 'ليلاً',
        };
    }

    protected function getDeviceBreakdown(Collection $behaviors): array
    {
        return $behaviors->groupBy('device_type')
            ->map(function ($items, $device) use ($behaviors) {
                return round(($items->count() / $behaviors->count()) * 100, 1);
            })->toArray();
    }

    public function getPopularProducts(int $limit = 10): Collection
    {
        $productViews = UserBehavior::where('event_type', 'product_view')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('event_target, count(*) as view_count')
            ->groupBy('event_target')
            ->orderByDesc('view_count')
            ->limit($limit)
            ->pluck('event_target');

        return Product::whereIn('id', $productViews)->get();
    }

    public function getTrendingSearches(int $limit = 10): array
    {
        return UserBehavior::where('event_type', 'search')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw("JSON_EXTRACT(event_data, '$.query') as query, count(*) as search_count")
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->pluck('query')
            ->toArray();
    }
}
