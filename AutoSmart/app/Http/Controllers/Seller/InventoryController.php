<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $store = auth()->user()->store;
        $warehouses = $store->warehouses;

        $query = WarehouseStock::whereHas('warehouse', fn($q) => $q->where('store_id', $store->id))
            ->with(['warehouse', 'product']);

        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }
        if ($request->filled('low_stock')) {
            $query->whereRaw('quantity <= low_stock_threshold');
        }

        $stocks = $query->paginate(20);
        $lowStockCount = WarehouseStock::whereHas('warehouse', fn($q) => $q->where('store_id', $store->id))
            ->whereRaw('quantity <= low_stock_threshold')->count();

        return view('seller.inventory.index', compact('stocks', 'warehouses', 'lowStockCount'));
    }

    public function warehouses()
    {
        $warehouses = auth()->user()->store->warehouses;
        return view('seller.inventory.warehouses', compact('warehouses'));
    }

    public function storeWarehouse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
        ]);
        
        $validated['store_id'] = auth()->user()->store->id;
        $warehouse = Warehouse::create($validated);

        if (auth()->user()->store->warehouses()->count() === 1) {
            $warehouse->setAsDefault();
        }

        return back()->with('success', 'تم إضافة المستودع');
    }

    public function adjust(Request $request, Product $product)
    {
        if ($product->store_id !== auth()->user()->store->id) abort(403);

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out,adjustment',
            'notes' => 'nullable|string|max:500',
        ]);

        $stock = WarehouseStock::firstOrCreate(
            ['warehouse_id' => $validated['warehouse_id'], 'product_id' => $product->id],
            ['quantity' => 0]
        );

        $oldQty = $stock->quantity;
        
        if ($validated['type'] === 'adjustment') {
            $stock->update(['quantity' => $validated['quantity']]);
        } else {
            $stock->increment('quantity', $validated['type'] === 'in' ? $validated['quantity'] : -$validated['quantity']);
        }

        InventoryMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $validated['warehouse_id'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'quantity_before' => $oldQty,
            'quantity_after' => $stock->fresh()->quantity,
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
        ]);

        // Update main product quantity
        $product->update(['quantity' => $product->warehouseStocks()->sum('quantity')]);

        return back()->with('success', 'تم تحديث المخزون');
    }

    public function movements(Request $request)
    {
        $store = auth()->user()->store;
        
        $movements = InventoryMovement::whereHas('product', fn($q) => $q->where('store_id', $store->id))
            ->with(['product', 'warehouse', 'user'])
            ->latest()
            ->paginate(30);

        return view('seller.inventory.movements', compact('movements'));
    }
}
