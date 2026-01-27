<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'stores_count' => Store::count(),
            'products_count' => Product::count(),
            'orders_count' => Order::count(),
            'pending_stores' => Store::where('status', 'pending')->count(),
            'total_sales' => Order::delivered()->sum('total'),
            'this_month_sales' => Order::delivered()
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'pending_orders' => Order::pending()->count(),
        ];

        $recentOrders = Order::with(['user', 'store'])
            ->latest()
            ->take(10)
            ->get();

        $recentStores = Store::with('user')
            ->latest()
            ->take(5)
            ->get();

        $pendingStores = Store::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentStores', 'pendingStores'));
    }
}
