<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $store = auth()->user()->store;
        $period = $request->get('period', 'month');

        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        // إحصائيات عامة
        $stats = [
            'total_orders' => $store->orders()->where('created_at', '>=', $startDate)->count(),
            'total_sales' => $store->orders()->where('created_at', '>=', $startDate)->where('status', 'delivered')->sum('total'),
            'avg_order_value' => $store->orders()->where('created_at', '>=', $startDate)->where('status', 'delivered')->avg('total') ?? 0,
            'total_products_sold' => $store->orders()
                ->where('created_at', '>=', $startDate)
                ->where('status', 'delivered')
                ->withSum('items', 'quantity')
                ->get()
                ->sum('items_sum_quantity'),
        ];

        // المبيعات اليومية
        $dailySales = $store->orders()
            ->where('created_at', '>=', $startDate)
            ->where('status', 'delivered')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as orders'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // المنتجات الأكثر مبيعاً
        $topProducts = Product::where('store_id', $store->id)
            ->orderByDesc('sales_count')
            ->take(10)
            ->get();

        // توزيع حالات الطلبات
        $ordersByStatus = $store->orders()
            ->where('created_at', '>=', $startDate)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('seller.reports.index', compact('stats', 'dailySales', 'topProducts', 'ordersByStatus', 'period'));
    }

    public function export(Request $request)
    {
        $store = auth()->user()->store;
        $period = $request->get('period', 'month');

        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $orders = $store->orders()
            ->with(['user', 'items.product'])
            ->where('created_at', '>=', $startDate)
            ->get();

        $csv = "رقم الطلب,العميل,المجموع,الحالة,التاريخ\n";
        foreach ($orders as $order) {
            $csv .= "{$order->order_number},{$order->user->name},{$order->total},{$order->status_label},{$order->created_at->format('Y-m-d')}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="orders-report.csv"');
    }
}
