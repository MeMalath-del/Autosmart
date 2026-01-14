<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $store = auth()->user()->store;

        $query = WarrantyClaim::where('store_id', $store->id)
            ->with(['user', 'product', 'order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->latest()->paginate(20);

        $stats = [
            'pending' => WarrantyClaim::where('store_id', $store->id)->where('status', 'pending')->count(),
            'under_review' => WarrantyClaim::where('store_id', $store->id)->where('status', 'under_review')->count(),
            'approved' => WarrantyClaim::where('store_id', $store->id)->where('status', 'approved')->count(),
        ];

        return view('seller.warranty.index', compact('claims', 'stats'));
    }

    public function show(WarrantyClaim $warrantyClaim)
    {
        if ($warrantyClaim->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        $warrantyClaim->load(['user', 'product', 'order', 'orderItem']);

        return view('seller.warranty.show', compact('warrantyClaim'));
    }

    public function respond(Request $request, WarrantyClaim $warrantyClaim)
    {
        if ($warrantyClaim->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:under_review,approved,rejected',
            'store_response' => 'required|string|max:1000',
            'resolution' => 'required_if:status,approved|nullable|in:repair,replace,refund',
        ]);

        $warrantyClaim->update([
            'status' => $request->status,
            'store_response' => $request->store_response,
            'resolution' => $request->resolution,
            'resolved_at' => $request->status === 'approved' ? now() : null,
        ]);

        return back()->with('success', 'تم تحديث حالة المطالبة');
    }
}
