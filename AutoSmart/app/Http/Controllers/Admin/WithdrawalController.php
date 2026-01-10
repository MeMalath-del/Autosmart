<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with('user', 'wallet');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(20);

        $stats = [
            'pending' => WithdrawalRequest::where('status', 'pending')->count(),
            'pending_amount' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function approve(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        $withdrawal->approve();

        return back()->with('success', 'تمت الموافقة على طلب السحب');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض هذا الطلب');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $withdrawal->reject($request->reason);

        return back()->with('success', 'تم رفض طلب السحب وإرجاع المبلغ للمحفظة');
    }

    public function complete(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'يجب الموافقة على الطلب أولاً');
        }

        $withdrawal->complete();

        return back()->with('success', 'تم إكمال عملية السحب');
    }
}
