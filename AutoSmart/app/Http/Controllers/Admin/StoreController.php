<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with('user')->withCount('products');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $stores = $query->latest()->paginate(20);

        return view('admin.stores.index', compact('stores'));
    }

    public function show(Store $store)
    {
        $store->load(['user', 'products', 'orders']);

        return view('admin.stores.show', compact('store'));
    }

    public function approve(Store $store)
    {
        $store->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم قبول المتجر');
    }

    public function reject(Store $store, Request $request)
    {
        $store->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'تم رفض المتجر');
    }

    public function suspend(Store $store)
    {
        $store->update(['status' => 'suspended']);

        return back()->with('success', 'تم تعليق المتجر');
    }

    public function activate(Store $store)
    {
        $store->update(['status' => 'approved']);

        return back()->with('success', 'تم تفعيل المتجر');
    }

    public function toggleFeatured(Store $store)
    {
        $store->update(['is_featured' => ! $store->is_featured]);

        return back()->with('success', 'تم تحديث حالة التميز');
    }

    public function toggleVerified(Store $store)
    {
        $store->update(['is_verified' => ! $store->is_verified]);

        return back()->with('success', 'تم تحديث حالة التوثيق');
    }
}
