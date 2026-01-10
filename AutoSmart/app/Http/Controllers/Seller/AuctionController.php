<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'seller']); }

    public function index()
    {
        $store = auth()->user()->store;
        $auctions = Auction::where('store_id', $store->id)
            ->with('product')
            ->latest()
            ->paginate(20);
        
        return view('seller.auctions.index', compact('auctions'));
    }

    public function create()
    {
        $products = auth()->user()->store->products()->active()->get();
        return view('seller.auctions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'starting_price' => 'required|numeric|min:1',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'bid_increment' => 'required|numeric|min:1',
            'starts_at' => 'required|date|after_or_equal:now',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        $validated['store_id'] = auth()->user()->store->id;
        $validated['status'] = 'draft';

        $auction = Auction::create($validated);

        return redirect()->route('seller.auctions.show', $auction)
            ->with('success', 'تم إنشاء المزاد');
    }

    public function show(Auction $auction)
    {
        if ($auction->store_id !== auth()->user()->store->id) abort(403);
        $auction->load(['product', 'bids.user']);
        return view('seller.auctions.show', compact('auction'));
    }

    public function activate(Auction $auction)
    {
        if ($auction->store_id !== auth()->user()->store->id) abort(403);
        $auction->update(['status' => 'active']);
        return back()->with('success', 'تم تفعيل المزاد');
    }

    public function cancel(Auction $auction)
    {
        if ($auction->store_id !== auth()->user()->store->id) abort(403);
        if ($auction->bids_count > 0) {
            return back()->with('error', 'لا يمكن إلغاء مزاد به مزايدات');
        }
        $auction->update(['status' => 'cancelled']);
        return back()->with('success', 'تم إلغاء المزاد');
    }
}
