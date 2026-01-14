<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store:id,name,slug', 'category:id,name,slug', 'images'])
            ->active()
            ->whereHas('store', fn ($q) => $q->approved());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->whereHas('carModels.brand', fn ($q) => $q->where('id', $request->brand));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('sales_count'),
            'rating' => $query->orderByDesc('rating'),
            default => $query->latest(),
        };

        return response()->json(
            $query->paginate($request->get('per_page', 20))
        );
    }

    public function show(Product $product)
    {
        if (! $product->is_active) {
            return response()->json(['message' => 'المنتج غير متوفر'], 404);
        }

        $product->load(['store', 'category', 'images', 'carModels.brand', 'reviews.user']);
        $product->incrementViews();

        return response()->json($product);
    }

    public function categories()
    {
        $categories = Category::active()
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    public function carBrands()
    {
        $brands = CarBrand::active()
            ->with('models:id,brand_id,name,name_ar')
            ->orderBy('name')
            ->get();

        return response()->json($brands);
    }

    public function featured()
    {
        $products = Product::with(['store:id,name,slug', 'images'])
            ->active()
            ->featured()
            ->inStock()
            ->take(10)
            ->get();

        return response()->json($products);
    }
}
