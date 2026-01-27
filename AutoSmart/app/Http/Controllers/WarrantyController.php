<?php

namespace App\Http\Controllers;

use App\Models\WarrantyClaim;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $claims = WarrantyClaim::where('user_id', auth()->id())
            ->with(['product', 'store', 'order'])
            ->latest()
            ->paginate(10);

        return view('warranty.index', compact('claims'));
    }

    public function create(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // التحقق من أن المنتج لا يزال تحت الضمان
        $product = $orderItem->product;
        
        return view('warranty.create', compact('orderItem', 'order', 'product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'issue_description' => 'required|string|max:2000',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $orderItem = OrderItem::with('order', 'product')->findOrFail($validated['order_item_id']);
        
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403);
        }

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('warranty-claims', 'public');
            }
        }

        WarrantyClaim::create([
            'user_id' => auth()->id(),
            'order_id' => $orderItem->order_id,
            'order_item_id' => $orderItem->id,
            'product_id' => $orderItem->product_id,
            'store_id' => $orderItem->order->store_id,
            'issue_description' => $validated['issue_description'],
            'images' => !empty($images) ? $images : null,
        ]);

        return redirect()->route('warranty.index')
            ->with('success', 'تم إرسال طلب الضمان');
    }

    public function show(WarrantyClaim $warrantyClaim)
    {
        if ($warrantyClaim->user_id !== auth()->id()) {
            abort(403);
        }

        $warrantyClaim->load(['product', 'store', 'order', 'orderItem']);

        return view('warranty.show', compact('warrantyClaim'));
    }
}
