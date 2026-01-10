<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['store:id,name,slug', 'items.product:id,name,slug'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $order->load(['store', 'items.product.images']);

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'nullable|exists:addresses,id',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,wallet',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->with('items.product.store')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'السلة فارغة'], 400);
        }

        // التحقق من الكوبون
        $couponDiscount = 0;
        $coupon = null;
        if (!empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();
            if ($coupon && $coupon->canBeUsedBy($request->user())) {
                $couponDiscount = $coupon->calculateDiscount($cart->total);
            }
        }

        try {
            DB::beginTransaction();

            $itemsByStore = $cart->items->groupBy('product.store_id');
            $orders = [];

            foreach ($itemsByStore as $storeId => $storeItems) {
                $subtotal = $storeItems->sum(fn($item) => $item->product->current_price * $item->quantity);
                $shipping = 25;
                $tax = $subtotal * 0.15;
                $discount = $couponDiscount > 0 ? ($couponDiscount / $itemsByStore->count()) : 0;
                $total = $subtotal + $shipping + $tax - $discount;

                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'store_id' => $storeId,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping,
                    'tax' => $tax,
                    'discount' => $discount,
                    'coupon_id' => $coupon?->id,
                    'coupon_code' => $coupon?->code,
                    'coupon_discount' => $discount,
                    'total' => $total,
                    'payment_method' => $validated['payment_method'],
                    'shipping_name' => $validated['shipping_name'],
                    'shipping_phone' => $validated['shipping_phone'],
                    'shipping_address' => $validated['shipping_address'],
                    'shipping_city' => $validated['shipping_city'],
                    'address_id' => $validated['address_id'],
                    'notes' => $validated['notes'],
                ]);

                foreach ($storeItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_sku' => $item->product->sku,
                        'price' => $item->product->current_price,
                        'quantity' => $item->quantity,
                        'total' => $item->product->current_price * $item->quantity,
                    ]);

                    $item->product->decrement('quantity', $item->quantity);
                    $item->product->increment('sales_count', $item->quantity);
                }

                $orders[] = $order;
            }

            // تسجيل استخدام الكوبون
            if ($coupon) {
                $coupon->increment('used_count');
            }

            $cart->clear();

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح',
                'orders' => $orders,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'حدث خطأ أثناء إنشاء الطلب'], 500);
        }
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        if (!$order->canBeCancelled()) {
            return response()->json(['message' => 'لا يمكن إلغاء هذا الطلب'], 400);
        }

        $order->cancel();

        return response()->json(['message' => 'تم إلغاء الطلب']);
    }
}
