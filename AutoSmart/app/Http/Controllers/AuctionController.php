<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auction::active()->with(['product.images', 'store']);

        if ($request->filled('category')) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $request->category));
        }

        $auctions = $query->orderBy('ends_at')->paginate(12);
        return view('auctions.index', compact('auctions'));
    }

    public function show(Auction $auction)
    {
        $auction->load(['product.images', 'store', 'bids.user']);
        $userBids = auth()->check() ? $auction->bids()->where('user_id', auth()->id())->get() : collect();
        return view('auctions.show', compact('auction', 'userBids'));
    }

    public function bid(Request $request, Auction $auction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . $auction->min_bid,
            'max_auto_bid' => 'nullable|numeric|min:' . $auction->min_bid
        ]);

        if (!$auction->isActive()) {
            return back()->with('error', 'المزاد منتهي');
        }

        $bid = $auction->placeBid(auth()->user(), $request->amount, $request->max_auto_bid);

        if (!$bid) {
            return back()->with('error', 'فشل في تقديم المزايدة');
        }

        return back()->with('success', 'تم تقديم مزايدتك بنجاح');
    }

    public function watch(Auction $auction)
    {
        $auction->watchers()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['notify_outbid' => true, 'notify_ending' => true]
        );
        return back()->with('success', 'تمت إضافة المزاد لقائمة المتابعة');
    }

    public function myBids()
    {
        $bids = AuctionBid::where('user_id', auth()->id())
            ->with('auction.product')
            ->latest()
            ->paginate(20);
        return view('auctions.my-bids', compact('bids'));
    }
}
