<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $store = auth()->user()->store;
        $period = $request->get('period', 30);
        $startDate = now()->subDays($period);

        // Daily analytics
        $dailyData = StoreAnalytics::where('store_id', $store->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get();

        // Summary
        $summary = [
            'total_views' => $dailyData->sum('views'),
            'unique_visitors' => $dailyData->sum('unique_visitors'),
            'total_orders' => $dailyData->sum('orders'),
            'total_revenue' => $dailyData->sum('revenue'),
            'avg_conversion' => $dailyData->avg('conversion_rate'),
        ];

        // Top products
        $topProducts = Product::where('store_id', $store->id)
            ->orderByDesc('views')
            ->take(10)
            ->get();

        // Top selling
        $topSelling = Product::where('store_id', $store->id)
            ->orderByDesc('sales_count')
            ->take(10)
            ->get();

        // Orders by status
        $ordersByStatus = Order::where('store_id', $store->id)
            ->where('created_at', '>=', $startDate)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Revenue by day
        $revenueByDay = Order::where('store_id', $store->id)
            ->where('status', 'delivered')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('seller.analytics.index', compact(
            'dailyData', 'summary', 'topProducts', 'topSelling',
            'ordersByStatus', 'revenueByDay', 'period'
        ));
    }

    public function products(Request $request)
    {
        $store = auth()->user()->store;

        $products = Product::where('store_id', $store->id)
            ->withCount('orderItems')
            ->orderByDesc($request->get('sort', 'views'))
            ->paginate(20);

        return view('seller.analytics.products', compact('products'));
    }
}
