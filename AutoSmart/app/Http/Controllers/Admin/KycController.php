<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $pending = KycVerification::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at')
            ->get();
        
        $verifications = KycVerification::whereIn('status', ['under_review', 'approved', 'rejected'])
            ->with(['user', 'reviewer'])
            ->orderByDesc('updated_at')
            ->paginate(20);
        
        $stats = [
            'pending' => KycVerification::where('status', 'pending')->count(),
            'under_review' => KycVerification::where('status', 'under_review')->count(),
            'approved_this_month' => KycVerification::where('status', 'approved')
                ->where('reviewed_at', '>=', now()->startOfMonth())
                ->count(),
            'rejected_this_month' => KycVerification::where('status', 'rejected')
                ->where('reviewed_at', '>=', now()->startOfMonth())
                ->count(),
        ];
        
        return view('admin.kyc.index', compact('pending', 'verifications', 'stats'));
    }

    public function show(KycVerification $verification)
    {
        $verification->load(['user', 'reviewer']);
        
        return view('admin.kyc.show', compact('verification'));
    }

    public function startReview(KycVerification $verification)
    {
        if ($verification->status !== 'pending') {
            return back()->with('error', 'هذا الطلب قيد المراجعة بالفعل');
        }
        
        $verification->update(['status' => 'under_review']);
        
        return redirect()->route('admin.kyc.show', $verification);
    }

    public function approve(KycVerification $verification)
    {
        $verification->approve(auth()->id());
        
        // Update user verification status
        $verification->user->update(['is_verified' => true]);
        
        // Notify user
        // $verification->user->notify(new KycApprovedNotification());
        
        return redirect()->route('admin.kyc.index')
            ->with('success', 'تمت الموافقة على التحقق');
    }

    public function reject(Request $request, KycVerification $verification)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        $verification->reject(auth()->id(), $request->rejection_reason);
        
        // Notify user
        // $verification->user->notify(new KycRejectedNotification($request->rejection_reason));
        
        return redirect()->route('admin.kyc.index')
            ->with('success', 'تم رفض طلب التحقق');
    }
}
