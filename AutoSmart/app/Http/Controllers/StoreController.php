<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::approved()
            ->withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $sort = $request->get('sort', 'rating');
        switch ($sort) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'products':
                $query->orderByDesc('products_count');
                break;
            default:
                $query->orderByDesc('rating');
        }

        $stores = $query->paginate(12)->withQueryString();

        $cities = Store::approved()
            ->distinct()
            ->pluck('city')
            ->filter();

        return view('stores.index', compact('stores', 'cities'));
    }

    public function show(Store $store)
    {
        if (! $store->isApproved()) {
            abort(404);
        }

        $store->load(['user', 'reviews.user']);

        $products = $store->products()
            ->with(['category', 'images'])
            ->active()
            ->latest()
            ->paginate(12);

        return view('stores.show', compact('store', 'products'));
    }
}
