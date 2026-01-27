<?php

namespace App\Services;

use App\Models\DynamicPricingRule;
use App\Models\PriceHistory;
use App\Models\Product;

class DynamicPricingService
{
    public function calculatePrice(Product $product): float
    {
        $basePrice = $product->price;
        $rules = DynamicPricingRule::active()
            ->where(fn ($q) => $q->where('product_id', $product->id)
                ->orWhere('category_id', $product->category_id)
                ->orWhere('store_id', $product->store_id)
                ->orWhereNull('product_id'))
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($rules as $rule) {
            if ($this->evaluateConditions($rule, $product)) {
                $basePrice = $rule->calculatePrice($basePrice);
            }
        }

        return round($basePrice, 2);
    }

    protected function evaluateConditions(DynamicPricingRule $rule, Product $product): bool
    {
        $conditions = $rule->conditions ?? [];

        // Demand-based pricing
        if ($rule->type === 'demand') {
            $recentOrders = $product->orderItems()->where('created_at', '>=', now()->subDays(7))->count();
            $threshold = $conditions['demand_threshold'] ?? 10;

            return $recentOrders >= $threshold;
        }

        // Inventory-based pricing
        if ($rule->type === 'inventory') {
            $lowStockThreshold = $conditions['low_stock_threshold'] ?? 5;
            $highStockThreshold = $conditions['high_stock_threshold'] ?? 100;

            if ($product->quantity <= $lowStockThreshold) {
                return true;
            }
            if ($product->quantity >= $highStockThreshold) {
                return true;
            }
        }

        // Time-based pricing
        if ($rule->type === 'time') {
            $hours = $conditions['hours'] ?? [];
            $days = $conditions['days'] ?? [];

            if (! empty($hours) && ! in_array(now()->hour, $hours)) {
                return false;
            }
            if (! empty($days) && ! in_array(now()->dayOfWeek, $days)) {
                return false;
            }

            return true;
        }

        return true;
    }

    public function updateProductPrices(): array
    {
        $updated = [];
        $products = Product::active()->get();

        foreach ($products as $product) {
            $newPrice = $this->calculatePrice($product);

            if ($newPrice !== (float) $product->current_price) {
                PriceHistory::create([
                    'product_id' => $product->id,
                    'old_price' => $product->current_price,
                    'new_price' => $newPrice,
                    'reason' => 'dynamic_pricing',
                ]);

                $product->update(['sale_price' => $newPrice]);
                $updated[] = $product;
            }
        }

        return $updated;
    }
}
