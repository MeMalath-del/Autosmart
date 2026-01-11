<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryAlert;
use App\Services\InventoryForecastService;
use Illuminate\Http\Request;

class InventoryAlertController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'role:admin']); }

    public function index(InventoryForecastService $service)
    {
        $alerts = InventoryAlert::with(['product', 'store'])
            ->where('is_resolved', false)
            ->orderBy('predicted_days_left')
            ->paginate(30);

        $stats = [
            'low_stock' => InventoryAlert::where('type', 'low_stock')->where('is_resolved', false)->count(),
            'out_of_stock' => InventoryAlert::where('type', 'out_of_stock')->where('is_resolved', false)->count(),
            'total_products' => InventoryAlert::where('is_resolved', false)->distinct('product_id')->count(),
        ];

        return view('admin.inventory.alerts', compact('alerts', 'stats'));
    }

    public function resolve(InventoryAlert $alert)
    {
        $alert->update(['is_resolved' => true, 'resolved_at' => now()]);
        return back()->with('success', 'تم حل التنبيه');
    }

    public function generateForecasts(InventoryForecastService $service)
    {
        $service->generateForecasts();
        return back()->with('success', 'تم تحديث التوقعات');
    }
}
