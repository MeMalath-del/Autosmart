<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PartRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $store = auth()->user()->store;

        $stats = [
            'products_count' => $store->products()->count(),
            'orders_count' => $store->orders()->count(),
            'pending_orders' => $store->orders()->pending()->count(),
            'total_sales' => $store->orders()->delivered()->sum('total'),
            'this_month_sales' => $store->orders()
                ->delivered()
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];

        $recentOrders = $store->orders()
            ->with(['user', 'items'])
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = $store->products()
            ->where('quantity', '<=', 5)
            ->where('quantity', '>', 0)
            ->take(5)
            ->get();

        $partRequests = PartRequest::active()
            ->with(['user', 'carBrand', 'carModel'])
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact('store', 'stats', 'recentOrders', 'lowStockProducts', 'partRequests'));
    }
}
