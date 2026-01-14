<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use Illuminate\Http\Request;

class InfluencerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Influencer::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $influencers = $query->latest()->paginate(20);

        $stats = [
            'total' => Influencer::count(),
            'pending' => Influencer::where('status', 'pending')->count(),
            'approved' => Influencer::where('status', 'approved')->count(),
            'total_earnings' => Influencer::sum('total_earnings'),
        ];

        return view('admin.influencers.index', compact('influencers', 'stats'));
    }

    public function show(Influencer $influencer)
    {
        $influencer->load(['user', 'sales.order']);

        return view('admin.influencers.show', compact('influencer'));
    }

    public function approve(Influencer $influencer)
    {
        $influencer->update(['status' => 'approved']);

        return back()->with('success', 'تم اعتماد المؤثر');
    }

    public function reject(Influencer $influencer)
    {
        $influencer->update(['status' => 'suspended']);

        return back()->with('success', 'تم رفض المؤثر');
    }

    public function updateCommission(Request $request, Influencer $influencer)
    {
        $request->validate(['commission_rate' => 'required|numeric|min:0|max:50']);
        $influencer->update(['commission_rate' => $request->commission_rate]);

        return back()->with('success', 'تم تحديث نسبة العمولة');
    }

    public function verify(Influencer $influencer)
    {
        $influencer->update(['is_verified' => ! $influencer->is_verified]);

        return back()->with('success', $influencer->is_verified ? 'تم توثيق المؤثر' : 'تم إلغاء التوثيق');
    }
}
