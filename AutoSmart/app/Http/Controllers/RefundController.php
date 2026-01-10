<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $refunds = RefundRequest::where('user_id', auth()->id())
            ->with('order')
            ->latest()
            ->paginate(10);
        return view('refunds.index', compact('refunds'));
    }

    public function create(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        if (!in_array($order->status, ['delivered', 'shipped'])) {
            return back()->with('error', 'لا يمكن طلب استرداد لهذا الطلب');
        }
        return view('refunds.create', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:1000',
            'refund_type' => 'required|in:wallet,bank',
            'bank_name' => 'required_if:refund_type,bank|nullable|string|max:100',
            'account_number' => 'required_if:refund_type,bank|nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        if ($order->user_id !== auth()->id()) abort(403);

        $validated['user_id'] = auth()->id();
        $validated['amount'] = $order->total;

        RefundRequest::create($validated);

        return redirect()->route('refunds.index')
            ->with('success', 'تم إرسال طلب الاسترداد');
    }

    public function show(RefundRequest $refundRequest)
    {
        if ($refundRequest->user_id !== auth()->id()) abort(403);
        $refundRequest->load('order');
        return view('refunds.show', compact('refundRequest'));
    }
}
