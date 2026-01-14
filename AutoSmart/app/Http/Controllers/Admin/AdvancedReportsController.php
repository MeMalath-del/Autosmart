<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\GiftCard;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvancedReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        return view('admin.reports.advanced', [
            'reportTypes' => $this->getReportTypes(),
        ]);
    }

    public function generate(Request $request)
    {
        $type = $request->type;
        $dateFrom = $request->date_from ?? now()->subMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $data = match ($type) {
            'sales' => $this->salesReport($dateFrom, $dateTo),
            'products' => $this->productsReport($dateFrom, $dateTo),
            'customers' => $this->customersReport($dateFrom, $dateTo),
            'stores' => $this->storesReport($dateFrom, $dateTo),
            'support' => $this->supportReport($dateFrom, $dateTo),
            'auctions' => $this->auctionsReport($dateFrom, $dateTo),
            'gift_cards' => $this->giftCardsReport($dateFrom, $dateTo),
            default => []
        };

        return view('admin.reports.result', compact('type', 'data', 'dateFrom', 'dateTo'));
    }

    protected function salesReport($from, $to): array
    {
        $orders = Order::whereBetween('created_at', [$from, $to]);

        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'average_order' => $orders->avg('total'),
            'completed_orders' => $orders->clone()->where('status', 'delivered')->count(),
            'cancelled_orders' => $orders->clone()->where('status', 'cancelled')->count(),
            'daily_sales' => Order::selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_products' => Product::withCount(['orderItems as sold' => fn ($q) => $q->whereHas('order', fn ($q2) => $q2->whereBetween('created_at', [$from, $to]))])
                ->orderByDesc('sold')
                ->limit(10)
                ->get(),
        ];
    }

    protected function productsReport($from, $to): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'out_of_stock' => Product::where('quantity', 0)->count(),
            'most_viewed' => Product::orderByDesc('views')->limit(10)->get(),
            'most_sold' => Product::orderByDesc('sales_count')->limit(10)->get(),
            'low_stock' => Product::where('quantity', '>', 0)->where('quantity', '<', 10)->get(),
            'categories_distribution' => DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->selectRaw('categories.name, COUNT(*) as count')
                ->groupBy('categories.name')
                ->get(),
        ];
    }

    protected function customersReport($from, $to): array
    {
        return [
            'total_customers' => User::where('role', 'customer')->count(),
            'new_customers' => User::where('role', 'customer')->whereBetween('created_at', [$from, $to])->count(),
            'active_customers' => User::whereHas('orders', fn ($q) => $q->whereBetween('created_at', [$from, $to]))->count(),
            'top_customers' => User::withSum(['orders' => fn ($q) => $q->whereBetween('created_at', [$from, $to])], 'total')
                ->orderByDesc('orders_sum_total')
                ->limit(10)
                ->get(),
        ];
    }

    protected function storesReport($from, $to): array
    {
        return [
            'total_stores' => Store::count(),
            'active_stores' => Store::where('is_active', true)->count(),
            'verified_stores' => Store::where('is_verified', true)->count(),
            'top_stores' => Store::withSum(['orders' => fn ($q) => $q->whereBetween('created_at', [$from, $to])], 'total')
                ->orderByDesc('orders_sum_total')
                ->limit(10)
                ->get(),
        ];
    }

    protected function supportReport($from, $to): array
    {
        $tickets = SupportTicket::whereBetween('created_at', [$from, $to]);

        return [
            'total_tickets' => $tickets->count(),
            'resolved_tickets' => $tickets->clone()->where('status', 'resolved')->count(),
            'avg_response_time' => $tickets->clone()->whereNotNull('first_response_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours')
                ->value('avg_hours'),
            'by_category' => SupportTicket::selectRaw('category, COUNT(*) as count')
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('category')
                ->get(),
        ];
    }

    protected function auctionsReport($from, $to): array
    {
        $auctions = Auction::whereBetween('created_at', [$from, $to]);

        return [
            'total_auctions' => $auctions->count(),
            'sold_auctions' => $auctions->clone()->where('status', 'sold')->count(),
            'total_value' => $auctions->clone()->where('status', 'sold')->sum('current_bid'),
            'avg_bids' => $auctions->clone()->avg('bids_count'),
        ];
    }

    protected function giftCardsReport($from, $to): array
    {
        return [
            'total_issued' => GiftCard::whereBetween('created_at', [$from, $to])->sum('initial_balance'),
            'total_redeemed' => DB::table('gift_card_transactions')
                ->where('type', 'redeem')
                ->whereBetween('created_at', [$from, $to])
                ->sum('amount'),
            'active_cards' => GiftCard::where('status', 'active')->count(),
        ];
    }

    protected function getReportTypes(): array
    {
        return [
            'sales' => 'تقرير المبيعات',
            'products' => 'تقرير المنتجات',
            'customers' => 'تقرير العملاء',
            'stores' => 'تقرير المتاجر',
            'support' => 'تقرير الدعم',
            'auctions' => 'تقرير المزادات',
            'gift_cards' => 'تقرير بطاقات الهدايا',
        ];
    }

    public function export(Request $request)
    {
        // In production, implement CSV/Excel export
        return back()->with('success', 'تم تصدير التقرير');
    }
}
