<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliatePayout;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Affiliate::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $affiliates = $query->latest()->paginate(20);

        $stats = [
            'total' => Affiliate::count(),
            'pending' => Affiliate::where('status', 'pending')->count(),
            'approved' => Affiliate::where('status', 'approved')->count(),
            'total_earnings' => Affiliate::sum('total_earnings'),
        ];

        return view('admin.affiliates.index', compact('affiliates', 'stats'));
    }

    public function show(Affiliate $affiliate)
    {
        $affiliate->load(['user', 'links', 'sales.order', 'payouts']);

        return view('admin.affiliates.show', compact('affiliate'));
    }

    public function approve(Affiliate $affiliate)
    {
        $affiliate->update(['status' => 'approved']);

        return back()->with('success', 'تم اعتماد الشريك');
    }

    public function updateCommission(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate(['commission_rate' => 'required|numeric|min:0|max:50']);
        $affiliate->update($validated);

        return back()->with('success', 'تم تحديث نسبة العمولة');
    }

    public function payouts()
    {
        $payouts = AffiliatePayout::with('affiliate.user')->latest()->paginate(20);

        return view('admin.affiliates.payouts', compact('payouts'));
    }

    public function processPayout(AffiliatePayout $payout)
    {
        $payout->update(['status' => 'completed', 'processed_at' => now()]);
        $payout->affiliate->decrement('pending_earnings', $payout->amount);
        $payout->affiliate->increment('paid_earnings', $payout->amount);

        return back()->with('success', 'تم معالجة الدفعة');
    }
}
