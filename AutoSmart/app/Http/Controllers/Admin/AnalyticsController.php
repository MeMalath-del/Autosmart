<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\SearchLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 30);
        $startDate = now()->subDays($period);

        // Main stats
        $stats = [
            'total_revenue' => Order::where('status', 'delivered')->where('created_at', '>=', $startDate)->sum('total'),
            'total_orders' => Order::where('created_at', '>=', $startDate)->count(),
            'new_users' => User::where('created_at', '>=', $startDate)->count(),
            'avg_order_value' => Order::where('status', 'delivered')->where('created_at', '>=', $startDate)->avg('total'),
        ];

        // Revenue by day
        $revenueByDay = Order::where('status', 'delivered')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products
        $topProducts = Product::withCount(['orderItems as sold' => fn ($q) => $q->whereHas('order', fn ($q2) => $q2->where('created_at', '>=', $startDate))])
            ->orderByDesc('sold')
            ->take(10)
            ->get();

        // Popular searches
        $popularSearches = SearchLog::getPopularSearches(10);

        // Orders by status
        $ordersByStatus = Order::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // New users by day
        $newUsersByDay = User::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.analytics.index', compact(
            'stats', 'revenueByDay', 'topProducts', 'popularSearches',
            'ordersByStatus', 'newUsersByDay', 'period'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->get('start', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end', now()->toDateString());

        $orders = Order::with('store')
            ->whereBetween('created_at', [$startDate, $endDate.' 23:59:59'])
            ->where('status', 'delivered')
            ->get();

        $byStore = $orders->groupBy('store_id')->map(fn ($items) => [
            'count' => $items->count(),
            'revenue' => $items->sum('total'),
        ]);

        return view('admin.analytics.sales', compact('orders', 'byStore', 'startDate', 'endDate'));
    }
}
