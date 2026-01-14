<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use App\Models\InstallmentRequest;
use App\Services\InstallmentService;
use Illuminate\Http\Request;

class InstallmentAdminController extends Controller
{
    protected InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $pendingRequests = InstallmentRequest::where('status', 'pending')
            ->with(['user', 'plan', 'order'])
            ->orderBy('created_at')
            ->get();

        $activeInstallments = InstallmentRequest::where('status', 'active')
            ->with(['user', 'plan', 'payments'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'pending' => InstallmentRequest::where('status', 'pending')->count(),
            'active' => InstallmentRequest::where('status', 'active')->count(),
            'total_financed' => InstallmentRequest::where('status', 'active')->sum('financed_amount'),
            'overdue_payments' => $this->installmentService->getOverduePayments()->count(),
        ];

        return view('admin.installments.index', compact('pendingRequests', 'activeInstallments', 'stats'));
    }

    public function show(InstallmentRequest $installment)
    {
        $installment->load(['user', 'plan', 'payments', 'order']);

        return view('admin.installments.show', compact('installment'));
    }

    public function approve(InstallmentRequest $installment)
    {
        if ($installment->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        $this->installmentService->approveRequest($installment);

        return back()->with('success', 'تمت الموافقة على طلب التقسيط');
    }

    public function reject(Request $request, InstallmentRequest $installment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $this->installmentService->rejectRequest($installment, $request->rejection_reason);

        return back()->with('success', 'تم رفض طلب التقسيط');
    }

    public function plans()
    {
        $plans = InstallmentPlan::withCount('requests')->get();

        return view('admin.installments.plans', compact('plans'));
    }

    public function createPlan()
    {
        return view('admin.installments.create-plan');
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'name_ar' => 'required|string|max:200',
            'months' => 'required|integer|min:1|max:60',
            'interest_rate' => 'required|numeric|min:0|max:50',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'down_payment_percentage' => 'required|numeric|min:0|max:100',
        ]);

        InstallmentPlan::create($request->all());

        return redirect()->route('admin.installments.plans')
            ->with('success', 'تم إنشاء خطة التقسيط');
    }

    public function togglePlan(InstallmentPlan $plan)
    {
        $plan->update(['is_active' => ! $plan->is_active]);

        return back()->with('success', 'تم تحديث حالة الخطة');
    }

    public function overduePayments()
    {
        $overduePayments = $this->installmentService->getOverduePayments();

        return view('admin.installments.overdue', compact('overduePayments'));
    }
}
