<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyTransaction;
use App\Services\AdvancedLoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    protected $loyalty;

    public function __construct(AdvancedLoyaltyService $loyalty)
    {
        $this->middleware('auth');
        $this->loyalty = $loyalty;
    }

    public function index()
    {
        $user = auth()->user();
        $tier = $this->loyalty->getUserTier($user);
        $loyaltyPoints = $user->loyaltyPoints;
        $transactions = LoyaltyTransaction::where('user_id', $user->id)->latest()->take(20)->get();
        $redemptions = $this->loyalty->getAvailableRedemptions($user);
        $earningActions = $this->loyalty->getEarningActions();
        $tiers = $this->loyalty->getTiers();

        return view('loyalty.index', compact('tier', 'loyaltyPoints', 'transactions', 'redemptions', 'earningActions', 'tiers'));
    }

    public function redeem(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:100',
        ]);

        $user = auth()->user();
        $success = $this->loyalty->redeemPoints($user, $validated['points']);

        if (!$success) {
            return back()->with('error', 'رصيدك غير كافٍ');
        }

        $value = $this->loyalty->getPointsValue($validated['points']);
        
        // Add discount to session or wallet
        session(['loyalty_discount' => $value]);

        return back()->with('success', 'تم استبدال ' . $validated['points'] . ' نقطة بخصم ' . $value . ' ر.س');
    }

    public function earnFromAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string',
            'reference_id' => 'nullable|integer',
        ]);

        $points = $this->loyalty->earnPointsFromAction(auth()->user(), $validated['action'], $validated['reference_id']);

        return response()->json(['success' => true, 'points_earned' => $points]);
    }
}
