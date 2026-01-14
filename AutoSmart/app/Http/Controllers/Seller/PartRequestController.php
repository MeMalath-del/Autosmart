<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PartRequest;
use App\Models\PartRequestQuote;
use Illuminate\Http\Request;

class PartRequestController extends Controller
{
    public function index()
    {
        $requests = PartRequest::active()
            ->with(['user', 'carBrand', 'carModel'])
            ->withCount('quotes')
            ->latest()
            ->paginate(20);

        $myQuotes = PartRequestQuote::where('store_id', auth()->user()->store->id)
            ->with(['partRequest.user'])
            ->latest()
            ->take(10)
            ->get();

        return view('seller.part-requests.index', compact('requests', 'myQuotes'));
    }

    public function show(PartRequest $partRequest)
    {
        $partRequest->load(['user', 'carBrand', 'carModel', 'quotes.store']);

        $myQuote = $partRequest->quotes()
            ->where('store_id', auth()->user()->store->id)
            ->first();

        return view('seller.part-requests.show', compact('partRequest', 'myQuote'));
    }

    public function submitQuote(Request $request, PartRequest $partRequest)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used,refurbished',
            'warranty' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'delivery_days' => 'nullable|integer|min:1|max:30',
        ]);

        $store = auth()->user()->store;

        // التحقق من عدم وجود عرض سابق
        $existingQuote = $partRequest->quotes()
            ->where('store_id', $store->id)
            ->first();

        if ($existingQuote) {
            $existingQuote->update($validated);

            return back()->with('success', 'تم تحديث العرض');
        }

        $partRequest->quotes()->create([
            'store_id' => $store->id,
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'warranty' => $validated['warranty'],
            'notes' => $validated['notes'],
            'delivery_days' => $validated['delivery_days'],
        ]);

        if ($partRequest->status === 'open') {
            $partRequest->update(['status' => 'quoted']);
        }

        return back()->with('success', 'تم إرسال العرض');
    }
}
