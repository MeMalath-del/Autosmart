<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    const TTL_SHORT = 300;      // 5 minutes
    const TTL_MEDIUM = 1800;    // 30 minutes
    const TTL_LONG = 3600;      // 1 hour
    const TTL_DAY = 86400;      // 24 hours

    public static function categories()
    {
        return Cache::remember('active_categories', self::TTL_LONG, function () {
            return \App\Models\Category::active()
                ->withCount('products')
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function carBrands()
    {
        return Cache::remember('active_car_brands', self::TTL_DAY, function () {
            return \App\Models\CarBrand::active()
                ->with('models')
                ->orderBy('name')
                ->get();
        });
    }

    public static function featuredProducts(int $limit = 8)
    {
        return Cache::remember("featured_products_{$limit}", self::TTL_MEDIUM, function () use ($limit) {
            return \App\Models\Product::with(['store:id,name,slug', 'images'])
                ->active()
                ->featured()
                ->inStock()
                ->take($limit)
                ->get();
        });
    }

    public static function featuredStores(int $limit = 6)
    {
        return Cache::remember("featured_stores_{$limit}", self::TTL_MEDIUM, function () use ($limit) {
            return \App\Models\Store::approved()
                ->featured()
                ->withCount('products')
                ->take($limit)
                ->get();
        });
    }

    public static function clear(string $key = null)
    {
        if ($key) {
            Cache::forget($key);
        } else {
            Cache::flush();
        }
    }

    public static function clearProductCache()
    {
        Cache::forget('featured_products_8');
        Cache::forget('featured_products_12');
    }

    public static function clearCategoryCache()
    {
        Cache::forget('active_categories');
    }

    public static function clearStoreCache()
    {
        Cache::forget('featured_stores_6');
        Cache::forget('featured_stores_12');
    }
}
