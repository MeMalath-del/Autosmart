<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Generate referral code if not exists
        if (! $user->referral_code) {
            $user->update(['referral_code' => strtoupper(Str::random(8))]);
        }

        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referred')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Referral::where('referrer_id', $user->id)->count(),
            'qualified' => Referral::where('referrer_id', $user->id)->where('status', '!=', 'pending')->count(),
            'earnings' => Referral::where('referrer_id', $user->id)->where('status', 'rewarded')->sum('referrer_reward'),
        ];

        return view('referral.index', compact('user', 'referrals', 'stats'));
    }

    public function apply(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $referrer = \App\Models\User::where('referral_code', strtoupper($request->code))->first();

        if (! $referrer) {
            return back()->with('error', 'كود الإحالة غير صحيح');
        }

        if ($referrer->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك استخدام كود إحالتك');
        }

        if (auth()->user()->referred_by) {
            return back()->with('error', 'لقد استخدمت كود إحالة سابقاً');
        }

        auth()->user()->update(['referred_by' => $referrer->id]);

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => auth()->id(),
            'referral_code' => $request->code,
            'referrer_reward' => 20,
            'referred_reward' => 10,
        ]);

        return back()->with('success', 'تم تطبيق كود الإحالة! ستحصل على مكافأتك عند أول طلب');
    }
}
