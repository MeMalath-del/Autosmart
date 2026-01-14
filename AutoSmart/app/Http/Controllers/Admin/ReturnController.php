<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = ReturnRequest::with(['user', 'order']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $returns = $query->latest()->paginate(20);

        $stats = [
            'pending' => ReturnRequest::where('status', 'pending')->count(),
            'approved' => ReturnRequest::where('status', 'approved')->count(),
            'completed' => ReturnRequest::where('status', 'completed')->count(),
            'total_refunded' => ReturnRequest::where('status', 'completed')->sum('refund_amount'),
        ];

        return view('admin.returns.index', compact('returns', 'stats'));
    }

    public function show(ReturnRequest $return)
    {
        $return->load(['user', 'order', 'items.orderItem.product']);

        return view('admin.returns.show', compact('return'));
    }

    public function approve(Request $request, ReturnRequest $return)
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'refund_method' => 'required|in:original,wallet,bank',
        ]);

        $return->approve(auth()->user(), $validated['refund_amount']);
        $return->update(['refund_method' => $validated['refund_method']]);

        return back()->with('success', 'تم الموافقة على طلب الإرجاع');
    }

    public function reject(Request $request, ReturnRequest $return)
    {
        $return->update(['status' => 'rejected', 'inspection_notes' => $request->reason]);

        return back()->with('success', 'تم رفض الطلب');
    }

    public function markReceived(ReturnRequest $return)
    {
        $return->markReceived();

        return back()->with('success', 'تم تأكيد استلام المرتجعات');
    }

    public function complete(ReturnRequest $return)
    {
        $return->complete();

        // Process refund based on method
        return back()->with('success', 'تم إتمام الإرجاع والاسترداد');
    }
}
