<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\InstallmentPlan;
use App\Models\InstallmentRequest;
use App\Models\InstallmentPayment;
use Illuminate\Support\Facades\DB;

class InstallmentService
{
    public function getAvailablePlans(float $amount): \Illuminate\Database\Eloquent\Collection
    {
        return InstallmentPlan::active()
            ->forAmount($amount)
            ->orderBy('months')
            ->get();
    }

    public function calculatePlanDetails(InstallmentPlan $plan, float $amount): array
    {
        $downPayment = $amount * ($plan->down_payment_percentage / 100);
        $financedAmount = $amount - $downPayment;
        $monthlyPayment = $plan->calculateMonthlyPayment($amount);
        $totalWithInterest = $plan->calculateTotalWithInterest($amount);
        
        return [
            'plan_id' => $plan->id,
            'plan_name' => $plan->localized_name,
            'months' => $plan->months,
            'interest_rate' => $plan->interest_rate,
            'total_amount' => $amount,
            'down_payment' => round($downPayment, 2),
            'financed_amount' => round($financedAmount, 2),
            'monthly_payment' => round($monthlyPayment, 2),
            'total_with_interest' => round($totalWithInterest, 2),
            'total_interest' => round($totalWithInterest - $amount, 2),
        ];
    }

    public function requestInstallment(User $user, Order $order, InstallmentPlan $plan): InstallmentRequest
    {
        $details = $this->calculatePlanDetails($plan, $order->total);
        
        return InstallmentRequest::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'plan_id' => $plan->id,
            'total_amount' => $details['total_amount'],
            'down_payment' => $details['down_payment'],
            'financed_amount' => $details['financed_amount'],
            'monthly_payment' => $details['monthly_payment'],
            'total_with_interest' => $details['total_with_interest'],
            'status' => 'pending',
        ]);
    }

    public function approveRequest(InstallmentRequest $request): void
    {
        DB::transaction(function() use ($request) {
            $request->update([
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addMonths($request->plan->months),
            ]);
            
            // Generate payment schedule
            $request->generatePaymentSchedule();
            
            // Update order status
            if ($request->order) {
                $request->order->update([
                    'payment_status' => 'installment_active',
                    'payment_method' => 'installment',
                ]);
            }
        });
    }

    public function rejectRequest(InstallmentRequest $request, string $reason): void
    {
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function processPayment(InstallmentPayment $payment, string $method, string $transactionId = null): void
    {
        DB::transaction(function() use ($payment, $method, $transactionId) {
            $payment->pay($method, $transactionId);
        });
    }

    public function getOverduePayments(): \Illuminate\Database\Eloquent\Collection
    {
        return InstallmentPayment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->with(['installmentRequest.user', 'installmentRequest.plan'])
            ->get();
    }

    public function getUserInstallments(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return InstallmentRequest::where('user_id', $user->id)
            ->with(['plan', 'payments', 'order'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function checkEligibility(User $user): array
    {
        // Simple eligibility check - in production integrate with credit scoring
        $eligible = true;
        $reasons = [];
        $maxAmount = 50000;
        
        // Check 1: Account age
        if ($user->created_at > now()->subMonths(1)) {
            $maxAmount = min($maxAmount, 5000);
            $reasons[] = 'حساب جديد - حد أقصى 5,000 ريال';
        }
        
        // Check 2: Previous purchases
        $previousPurchases = $user->orders()->where('payment_status', 'paid')->count();
        if ($previousPurchases < 2) {
            $maxAmount = min($maxAmount, 10000);
            $reasons[] = 'عدد مشتريات قليل - حد أقصى 10,000 ريال';
        }
        
        // Check 3: Active installments
        $activeInstallments = InstallmentRequest::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();
        
        if ($activeInstallments >= 2) {
            $eligible = false;
            $reasons[] = 'لديك بالفعل تقسيطات نشطة';
        }
        
        // Check 4: Overdue payments
        $overdueCount = InstallmentPayment::whereHas('installmentRequest', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'pending')
          ->where('due_date', '<', now())
          ->count();
        
        if ($overdueCount > 0) {
            $eligible = false;
            $reasons[] = 'لديك دفعات متأخرة';
        }
        
        return [
            'eligible' => $eligible,
            'max_amount' => $maxAmount,
            'reasons' => $reasons,
        ];
    }
}
