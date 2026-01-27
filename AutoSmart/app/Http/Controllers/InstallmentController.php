<?php

namespace App\Http\Controllers;

use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentRequest;
use App\Models\Order;
use App\Services\InstallmentService;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    protected InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
        $this->middleware('auth');
    }

    public function index()
    {
        $installments = $this->installmentService->getUserInstallments(auth()->user());
        $eligibility = $this->installmentService->checkEligibility(auth()->user());

        return view('installments.index', compact('installments', 'eligibility'));
    }

    public function calculator()
    {
        $plans = InstallmentPlan::active()->orderBy('months')->get();

        return view('installments.calculator', compact('plans'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'plan_id' => 'required|exists:installment_plans,id',
        ]);

        $plan = InstallmentPlan::findOrFail($request->plan_id);
        $details = $this->installmentService->calculatePlanDetails($plan, $request->amount);

        return response()->json($details);
    }

    public function apply(Request $request, Order $order)
    {
        $request->validate([
            'plan_id' => 'required|exists:installment_plans,id',
        ]);

        // Check eligibility
        $eligibility = $this->installmentService->checkEligibility(auth()->user());

        if (! $eligibility['eligible']) {
            return back()->with('error', 'غير مؤهل للتقسيط: '.implode(', ', $eligibility['reasons']));
        }

        if ($order->total > $eligibility['max_amount']) {
            return back()->with('error', "الحد الأقصى للتقسيط هو {$eligibility['max_amount']} ريال");
        }

        $plan = InstallmentPlan::findOrFail($request->plan_id);
        $installment = $this->installmentService->requestInstallment(auth()->user(), $order, $plan);

        return redirect()->route('installments.show', $installment)
            ->with('success', 'تم تقديم طلب التقسيط بنجاح');
    }

    public function show(InstallmentRequest $installment)
    {
        $this->authorize('view', $installment);

        $installment->load(['plan', 'payments', 'order']);

        return view('installments.show', compact('installment'));
    }

    public function pay(InstallmentPayment $payment, Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:card,mada,wallet',
        ]);

        $this->authorize('update', $payment->installmentRequest);

        // In production, integrate with payment gateway
        $this->installmentService->processPayment(
            $payment,
            $request->payment_method,
            'PAY-'.uniqid()
        );

        return back()->with('success', 'تم سداد القسط بنجاح');
    }
}
