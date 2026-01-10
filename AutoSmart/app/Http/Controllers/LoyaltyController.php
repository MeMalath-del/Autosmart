<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyPoints;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $loyalty = LoyaltyPoints::getOrCreate(auth()->id());
        $transactions = LoyaltyTransaction::where('user_id', auth()->id())
            ->latest()->paginate(20);
        
        $tiers = [
            'bronze' => ['name' => 'برونزي', 'min' => 0, 'multiplier' => 1],
            'silver' => ['name' => 'فضي', 'min' => 1000, 'multiplier' => 1.5],
            'gold' => ['name' => 'ذهبي', 'min' => 5000, 'multiplier' => 2],
            'platinum' => ['name' => 'بلاتيني', 'min' => 10000, 'multiplier' => 3],
        ];

        return view('loyalty.index', compact('loyalty', 'transactions', 'tiers'));
    }

    public function redeem(Request $request)
    {
        $request->validate(['points' => 'required|integer|min:100']);
        
        $loyalty = LoyaltyPoints::getOrCreate(auth()->id());
        
        if ($loyalty->points < $request->points) {
            return back()->with('error', 'رصيد النقاط غير كافي');
        }

        $sarValue = $request->points * 0.01; // كل 100 نقطة = 1 ر.س
        
        if ($loyalty->redeemPoints($request->points, 'استبدال نقاط برصيد المحفظة')) {
            $wallet = \App\Models\Wallet::getOrCreateForUser(auth()->id());
            $wallet->credit($sarValue, 'استبدال ' . $request->points . ' نقطة', 'loyalty', $loyalty->id);
            
            return back()->with('success', "تم استبدال {$request->points} نقطة بـ {$sarValue} ر.س");
        }

        return back()->with('error', 'حدث خطأ في استبدال النقاط');
    }
}
