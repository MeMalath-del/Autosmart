<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\RecentlyViewed;
use Illuminate\Support\Collection;

class RecommendationService
{
    public function getSimilarProducts(Product $product, int $limit = 8): Collection
    {
        return Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id)
                    ->orWhereHas('carModels', fn($q2) => 
                        $q2->whereIn('car_model_id', $product->carModels->pluck('id'))
                    );
            })
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    public function getFrequentlyBoughtTogether(Product $product, int $limit = 4): Collection
    {
        $orderIds = $product->orderItems()->pluck('order_id');
        
        if ($orderIds->isEmpty()) {
            return collect();
        }

        return Product::whereHas('orderItems', fn($q) => 
                $q->whereIn('order_id', $orderIds)->where('product_id', '!=', $product->id)
            )
            ->where('is_active', true)
            ->withCount(['orderItems' => fn($q) => $q->whereIn('order_id', $orderIds)])
            ->orderByDesc('order_items_count')
            ->take($limit)
            ->get();
    }

    public function getPersonalizedRecommendations(int $userId, int $limit = 12): Collection
    {
        // Based on user's orders
        $orderedProducts = Order::where('user_id', $userId)
            ->with('items.product')
            ->get()
            ->flatMap(fn($order) => $order->items->pluck('product'))
            ->filter();

        if ($orderedProducts->isEmpty()) {
            return Product::active()->featured()->take($limit)->get();
        }

        $categoryIds = $orderedProducts->pluck('category_id')->unique();
        $carModelIds = $orderedProducts->flatMap(fn($p) => $p->carModels->pluck('id'))->unique();

        return Product::whereNotIn('id', $orderedProducts->pluck('id'))
            ->where('is_active', true)
            ->where(function ($q) use ($categoryIds, $carModelIds) {
                $q->whereIn('category_id', $categoryIds)
                    ->orWhereHas('carModels', fn($q2) => $q2->whereIn('car_model_id', $carModelIds));
            })
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    public function getRecentlyViewed(int $limit = 10): Collection
    {
        return RecentlyViewed::getRecent($limit);
    }

    public function getForUserCar(int $userCarId, int $limit = 12): Collection
    {
        $userCar = \App\Models\UserCar::find($userCarId);
        if (!$userCar) return collect();

        return Product::active()
            ->whereHas('carModels', fn($q) => $q->where('car_model_id', $userCar->car_model_id))
            ->take($limit)
            ->get();
    }
}
