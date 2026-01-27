<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use Illuminate\Http\Request;

class B2BController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'role:admin']); }

    public function index(Request $request)
    {
        $query = BusinessAccount::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accounts = $query->latest()->paginate(20);
        
        $stats = [
            'total' => BusinessAccount::count(),
            'pending' => BusinessAccount::where('status', 'pending')->count(),
            'approved' => BusinessAccount::where('status', 'approved')->count(),
        ];

        return view('admin.b2b.index', compact('accounts', 'stats'));
    }

    public function show(BusinessAccount $account)
    {
        $account->load(['user', 'quoteRequests', 'creditInvoices']);
        return view('admin.b2b.show', compact('account'));
    }

    public function approve(BusinessAccount $account)
    {
        $account->update([
            'status' => 'approved',
            'is_verified' => true
        ]);
        
        // Notify user
        // $account->user->notify(new BusinessAccountApproved($account));

        return back()->with('success', 'تم اعتماد الحساب');
    }

    public function reject(Request $request, BusinessAccount $account)
    {
        $account->update(['status' => 'suspended']);
        return back()->with('success', 'تم رفض الحساب');
    }

    public function updateCredit(Request $request, BusinessAccount $account)
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0|max:90',
        ]);

        $account->update($validated);
        return back()->with('success', 'تم تحديث حد الائتمان');
    }
}
