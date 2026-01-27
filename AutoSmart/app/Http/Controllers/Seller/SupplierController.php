<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('store_id', auth()->user()->store->id)
            ->withCount('purchaseOrders')
            ->paginate(20);
        return view('seller.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:300',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);
        $validated['store_id'] = auth()->user()->store->id;
        Supplier::create($validated);
        return back()->with('success', 'تم إضافة المورد');
    }

    public function purchaseOrders()
    {
        $orders = PurchaseOrder::where('store_id', auth()->user()->store->id)
            ->with(['supplier', 'items'])
            ->latest()
            ->paginate(20);
        return view('seller.suppliers.orders', compact('orders'));
    }

    public function createPurchaseOrder()
    {
        $suppliers = Supplier::where('store_id', auth()->user()->store->id)->active()->get();
        $warehouses = auth()->user()->store->warehouses;
        $products = Product::where('store_id', auth()->user()->store->id)->get();
        return view('seller.suppliers.create-order', compact('suppliers', 'warehouses', 'products'));
    }

    public function storePurchaseOrder(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::create([
            'store_id' => auth()->user()->store->id,
            'supplier_id' => $validated['supplier_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'expected_date' => $validated['expected_date'],
            'notes' => $validated['notes'],
        ]);

        foreach ($validated['items'] as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total' => $item['quantity'] * $item['unit_cost'],
            ]);
        }

        $po->calculateTotals();

        return redirect()->route('seller.suppliers.orders')
            ->with('success', 'تم إنشاء طلب التوريد');
    }

    public function receivePurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->store_id !== auth()->user()->store->id) abort(403);
        
        $purchaseOrder->update(['status' => 'received', 'received_date' => now()]);
        
        // Add stock
        foreach ($purchaseOrder->items as $item) {
            $item->update(['received_quantity' => $item->quantity]);
            
            if ($purchaseOrder->warehouse_id) {
                $stock = \App\Models\WarehouseStock::firstOrCreate([
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'product_id' => $item->product_id
                ], ['quantity' => 0]);
                $stock->increment('quantity', $item->quantity);
            }
            
            $item->product->increment('quantity', $item->quantity);
        }

        return back()->with('success', 'تم استلام طلب التوريد وتحديث المخزون');
    }
}
