<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $store = auth()->user()->store;

        $query = $store->orders()->with(['user', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->latest()->paginate(20);

        $stats = [
            'pending' => $store->orders()->pending()->count(),
            'confirmed' => $store->orders()->confirmed()->count(),
            'processing' => $store->orders()->processing()->count(),
            'shipped' => $store->orders()->shipped()->count(),
            'delivered' => $store->orders()->delivered()->count(),
        ];

        return view('seller.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $this->authorize($order);
        $order->load(['user', 'items.product.images']);

        return view('seller.orders.show', compact('order'));
    }

    public function confirm(Order $order)
    {
        $this->authorize($order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'لا يمكن تأكيد هذا الطلب');
        }

        $order->confirm();

        return back()->with('success', 'تم تأكيد الطلب');
    }

    public function process(Order $order)
    {
        $this->authorize($order);

        if ($order->status !== 'confirmed') {
            return back()->with('error', 'لا يمكن معالجة هذا الطلب');
        }

        $order->update(['status' => 'processing']);

        return back()->with('success', 'تم بدء معالجة الطلب');
    }

    public function ship(Order $order, Request $request)
    {
        $this->authorize($order);

        if (! in_array($order->status, ['confirmed', 'processing'])) {
            return back()->with('error', 'لا يمكن شحن هذا الطلب');
        }

        $order->ship();

        return back()->with('success', 'تم شحن الطلب');
    }

    public function deliver(Order $order)
    {
        $this->authorize($order);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'لا يمكن تأكيد التوصيل');
        }

        $order->deliver();

        return back()->with('success', 'تم تأكيد التوصيل');
    }

    public function cancel(Order $order)
    {
        $this->authorize($order);

        if (! $order->canBeCancelled()) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب');
        }

        $order->cancel();

        return back()->with('success', 'تم إلغاء الطلب');
    }

    public function updateNotes(Request $request, Order $order)
    {
        $this->authorize($order);

        $order->update([
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'تم حفظ الملاحظات');
    }

    private function authorize(Order $order)
    {
        if ($order->store_id !== auth()->user()->store->id) {
            abort(403);
        }
    }
}
