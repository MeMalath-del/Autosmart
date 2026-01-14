<?php

namespace App\Services;

use App\Models\DemandForecast;
use App\Models\InventoryAlert;
use App\Models\Product;

class InventoryForecastService
{
    public function generateForecasts(): void
    {
        $products = Product::active()->with('orderItems')->get();

        foreach ($products as $product) {
            $this->forecastProduct($product);
        }
    }

    public function forecastProduct(Product $product): array
    {
        // Calculate average daily demand from last 30 days
        $last30Days = $product->orderItems()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as quantity')
            ->groupBy('date')
            ->pluck('quantity', 'date')
            ->toArray();

        $avgDailyDemand = count($last30Days) > 0 ? array_sum($last30Days) / count($last30Days) : 0;

        // Forecast for next 30 days
        $forecasts = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = now()->addDays($i)->toDateString();
            // Simple forecast with day-of-week adjustment
            $dayOfWeek = now()->addDays($i)->dayOfWeek;
            $adjustment = in_array($dayOfWeek, [5, 6]) ? 1.3 : 1.0; // Weekend boost

            $forecast = DemandForecast::updateOrCreate(
                ['product_id' => $product->id, 'forecast_date' => $date],
                [
                    'predicted_demand' => ceil($avgDailyDemand * $adjustment),
                    'confidence' => min(0.95, count($last30Days) / 30),
                    'factors' => ['avg_daily' => $avgDailyDemand, 'day_adjustment' => $adjustment],
                ]
            );
            $forecasts[] = $forecast;
        }

        // Calculate days until stockout
        $currentStock = $product->quantity;
        $daysLeft = 0;

        foreach ($forecasts as $forecast) {
            if ($currentStock <= 0) {
                break;
            }
            $currentStock -= $forecast->predicted_demand;
            $daysLeft++;
        }

        // Generate alert if needed
        if ($daysLeft <= 7 && $product->quantity > 0) {
            InventoryAlert::updateOrCreate(
                ['product_id' => $product->id, 'store_id' => $product->store_id, 'is_resolved' => false],
                [
                    'type' => $daysLeft <= 3 ? 'out_of_stock' : 'low_stock',
                    'threshold' => ceil($avgDailyDemand * 7),
                    'current_quantity' => $product->quantity,
                    'predicted_days_left' => $daysLeft,
                    'suggested_reorder_qty' => ceil($avgDailyDemand * 30),
                ]
            );
        }

        return [
            'avg_daily_demand' => $avgDailyDemand,
            'days_until_stockout' => $daysLeft,
            'suggested_reorder' => ceil($avgDailyDemand * 30),
        ];
    }

    public function getPendingAlerts(?int $storeId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = InventoryAlert::with('product')->where('is_resolved', false);
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->orderBy('predicted_days_left')->get();
    }
}
