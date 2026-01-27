<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateLink;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $affiliate = Affiliate::where('user_id', auth()->id())->first();
        
        if (!$affiliate) {
            return view('affiliate.apply');
        }
        
        if ($affiliate->status === 'pending') {
            return view('affiliate.pending', compact('affiliate'));
        }

        $links = $affiliate->links()->latest()->get();
        $sales = $affiliate->sales()->with('order')->latest()->paginate(20);
        $stats = [
            'clicks' => $affiliate->total_clicks,
            'orders' => $affiliate->total_orders,
            'conversion_rate' => $affiliate->conversion_rate,
            'pending_earnings' => $affiliate->pending_earnings,
            'total_earnings' => $affiliate->total_earnings,
        ];
        
        return view('affiliate.dashboard', compact('affiliate', 'links', 'sales', 'stats'));
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'website' => 'nullable|url',
            'bio' => 'required|string|max:1000',
            'payment_method' => 'required|in:bank,paypal',
            'payment_details' => 'required|array',
        ]);

        $validated['user_id'] = auth()->id();
        Affiliate::create($validated);

        return redirect()->route('affiliate.index')
            ->with('success', 'تم تقديم طلبك. سنراجعه قريباً');
    }

    public function createLink(Request $request)
    {
        $affiliate = Affiliate::where('user_id', auth()->id())->approved()->firstOrFail();
        
        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'destination_url' => 'required|url',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $link = $affiliate->links()->create($validated);
        
        return back()->with('success', 'تم إنشاء الرابط');
    }

    public function track(string $code)
    {
        $link = AffiliateLink::where('code', $code)->where('is_active', true)->firstOrFail();
        
        $click = $link->affiliate->recordClick(request()->ip());
        $link->increment('clicks');
        
        session(['affiliate_click_id' => $click->id, 'affiliate_code' => $link->affiliate->code]);
        
        return redirect($link->destination_url);
    }

    public function requestPayout(Request $request)
    {
        $affiliate = Affiliate::where('user_id', auth()->id())->approved()->firstOrFail();
        
        if (!$affiliate->canRequestPayout()) {
            return back()->with('error', 'الحد الأدنى للسحب هو ' . $affiliate->minimum_payout . ' ر.س');
        }

        $affiliate->payouts()->create([
            'amount' => $affiliate->pending_earnings,
            'payment_method' => $affiliate->payment_method,
            'payment_details' => $affiliate->payment_details,
        ]);

        return back()->with('success', 'تم تقديم طلب السحب');
    }
}
