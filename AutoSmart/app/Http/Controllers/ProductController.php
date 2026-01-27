<?php

namespace App\Http\Controllers;

use App\Models\CarBrand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category', 'images'])
            ->active()
            ->whereHas('store', fn ($q) => $q->approved());

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('part_number', 'like', "%{$search}%")
                    ->orWhere('oem_number', 'like', "%{$search}%");
            });
        }

        // فلترة حسب التصنيف
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // فلترة حسب ماركة السيارة
        if ($request->filled('brand')) {
            $query->whereHas('carModels.brand', fn ($q) => $q->where('id', $request->brand));
        }

        // فلترة حسب موديل السيارة
        if ($request->filled('model')) {
            $query->whereHas('carModels', fn ($q) => $q->where('id', $request->model));
        }

        // فلترة حسب الحالة
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // فلترة حسب السعر
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // فلترة المتوفر فقط
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // الترتيب
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderByDesc('sales_count');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(24)->withQueryString();

        $categories = Category::active()->parent()->get();
        $carBrands = CarBrand::active()->get();

        return view('products.index', compact('products', 'categories', 'carBrands'));
    }

    public function show(Product $product)
    {
        if (! $product->is_active || ! $product->store->isApproved()) {
            abort(404);
        }

        $product->load(['store', 'category', 'images', 'carModels.brand', 'reviews.user']);
        $product->incrementViews();

        $relatedProducts = Product::with(['store', 'images'])
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function category(Category $category)
    {
        $products = Product::with(['store', 'category', 'images'])
            ->active()
            ->where('category_id', $category->id)
            ->whereHas('store', fn ($q) => $q->approved())
            ->latest()
            ->paginate(24);

        return view('products.category', compact('category', 'products'));
    }

    public function search(Request $request)
    {
        return redirect()->route('products.index', $request->all());
    }
}
