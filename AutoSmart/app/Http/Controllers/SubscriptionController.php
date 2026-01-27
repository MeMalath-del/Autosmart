<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $plans = SubscriptionPlan::active()->get();
        $userSubscriptions = UserSubscription::where('user_id', auth()->id())
            ->with('plan')
            ->latest()
            ->get();

        return view('subscriptions.index', compact('plans', 'userSubscriptions'));
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'user_car_id' => 'nullable|exists:user_cars,id',
        ]);

        // Check existing active subscription
        $existing = UserSubscription::where('user_id', auth()->id())
            ->where('plan_id', $plan->id)
            ->active()
            ->first();

        if ($existing) {
            return back()->with('error', 'لديك اشتراك نشط في هذه الخطة');
        }

        $endsAt = match ($plan->billing_cycle) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => now()->addMonth()
        };

        $subscription = UserSubscription::create([
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'user_car_id' => $validated['user_car_id'] ?? null,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'next_billing_at' => $endsAt,
        ]);

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'تم الاشتراك بنجاح');
    }

    public function show(UserSubscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        $subscription->load(['plan', 'deliveries']);

        return view('subscriptions.show', compact('subscription'));
    }

    public function cancel(Request $request, UserSubscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        $subscription->cancel($request->reason);

        return back()->with('success', 'تم إلغاء الاشتراك');
    }

    public function pause(UserSubscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        $subscription->pause();

        return back()->with('success', 'تم إيقاف الاشتراك مؤقتاً');
    }

    public function resume(UserSubscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        $subscription->resume();

        return back()->with('success', 'تم استئناف الاشتراك');
    }
}
