<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $returns = ReturnRequest::where('user_id', auth()->id())
            ->with('order')
            ->latest()
            ->paginate(10);
        return view('returns.index', compact('returns'));
    }

    public function create(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        if (!in_array($order->status, ['delivered'])) {
            return back()->with('error', 'لا يمكن إرجاع هذا الطلب');
        }
        
        $order->load('items.product');
        return view('returns.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        
        $validated = $request->validate([
            'type' => 'required|in:return,exchange',
            'reason' => 'required|in:defective,wrong_item,not_as_described,changed_mind,other',
            'description' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('returns', 'public');
            }
        }

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'images' => $images ?: null,
        ]);

        foreach ($validated['items'] as $item) {
            $return->items()->create($item);
        }

        return redirect()->route('returns.show', $return)
            ->with('success', 'تم تقديم طلب الإرجاع');
    }

    public function show(ReturnRequest $return)
    {
        if ($return->user_id !== auth()->id()) abort(403);
        $return->load(['order', 'items.orderItem.product']);
        return view('returns.show', compact('return'));
    }
}
