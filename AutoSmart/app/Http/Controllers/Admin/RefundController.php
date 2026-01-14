<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundRequest::with(['user', 'order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->latest()->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function show(RefundRequest $refundRequest)
    {
        $refundRequest->load(['user', 'order.items']);

        return view('admin.refunds.show', compact('refundRequest'));
    }

    public function approve(RefundRequest $refundRequest)
    {
        $refundRequest->approve();

        return back()->with('success', 'تمت الموافقة على طلب الاسترداد');
    }

    public function reject(Request $request, RefundRequest $refundRequest)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $refundRequest->reject($request->reason);

        return back()->with('success', 'تم رفض طلب الاسترداد');
    }

    public function process(RefundRequest $refundRequest)
    {
        if ($refundRequest->status !== 'approved') {
            return back()->with('error', 'يجب الموافقة على الطلب أولاً');
        }

        $refundRequest->process();

        return back()->with('success', 'تم معالجة الاسترداد');
    }
}
