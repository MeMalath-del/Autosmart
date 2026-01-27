<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\CarBrand;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['store', 'category', 'images'])
            ->active()
            ->featured()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::with(['store', 'category', 'images'])
            ->active()
            ->inStock()
            ->latest()
            ->take(12)
            ->get();

        $categories = Category::active()
            ->parent()
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $featuredStores = Store::approved()
            ->featured()
            ->withCount('products')
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $carBrands = CarBrand::active()
            ->withCount('models')
            ->orderBy('name')
            ->get();

        return view('home', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'featuredStores',
            'carBrands'
        ));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}
