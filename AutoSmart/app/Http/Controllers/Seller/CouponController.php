<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $store = auth()->user()->store;
        $coupons = Coupon::where('store_id', $store->id)
            ->withCount('usages')
            ->latest()
            ->paginate(20);

        return view('seller.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('seller.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'value' => 'required_unless:type,free_shipping|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $validated['store_id'] = auth()->user()->store->id;
        $validated['code'] = $validated['code'] ?? strtoupper(Str::random(8));

        Coupon::create($validated);

        return redirect()->route('seller.coupons.index')
            ->with('success', 'تم إنشاء الكوبون');
    }

    public function edit(Coupon $coupon)
    {
        if ($coupon->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        return view('seller.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        if ($coupon->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'value' => 'required_unless:type,free_shipping|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return redirect()->route('seller.coupons.index')
            ->with('success', 'تم تحديث الكوبون');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        $coupon->delete();

        return back()->with('success', 'تم حذف الكوبون');
    }
}
