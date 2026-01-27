<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Auction::with(['store', 'product', 'winner']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $auctions = $query->latest()->paginate(20);

        $stats = [
            'active' => Auction::where('status', 'active')->count(),
            'ended' => Auction::where('status', 'ended')->count(),
            'sold' => Auction::where('status', 'sold')->count(),
            'total_value' => Auction::where('status', 'sold')->sum('current_bid'),
        ];

        return view('admin.auctions.index', compact('auctions', 'stats'));
    }

    public function show(Auction $auction)
    {
        $auction->load(['store', 'product', 'bids.user', 'winner']);

        return view('admin.auctions.show', compact('auction'));
    }

    public function feature(Auction $auction)
    {
        $auction->update(['is_featured' => ! $auction->is_featured]);

        return back()->with('success', $auction->is_featured ? 'تم تمييز المزاد' : 'تم إلغاء التمييز');
    }

    public function cancel(Auction $auction)
    {
        $auction->update(['status' => 'cancelled']);

        return back()->with('success', 'تم إلغاء المزاد');
    }
}
