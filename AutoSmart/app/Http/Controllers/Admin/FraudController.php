<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudAlert;
use App\Models\FraudRule;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;

class FraudController extends Controller
{
    protected FraudDetectionService $fraudService;

    public function __construct(FraudDetectionService $fraudService)
    {
        $this->fraudService = $fraudService;
        $this->middleware(['auth', 'admin']);
    }

    public function alerts()
    {
        $newAlerts = FraudAlert::new()->with(['user', 'order'])->orderByDesc('created_at')->get();
        
        $alerts = FraudAlert::whereIn('status', ['investigating', 'confirmed', 'false_positive'])
            ->with(['user', 'order'])
            ->orderByDesc('created_at')
            ->paginate(20);
        
        $stats = [
            'new' => FraudAlert::new()->count(),
            'investigating' => FraudAlert::where('status', 'investigating')->count(),
            'confirmed_this_month' => FraudAlert::where('status', 'confirmed')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'high_risk' => FraudAlert::highRisk()->new()->count(),
        ];
        
        return view('admin.fraud.alerts', compact('newAlerts', 'alerts', 'stats'));
    }

    public function showAlert(FraudAlert $alert)
    {
        $alert->load(['user', 'order.items.product', 'resolver']);
        
        // Get user's order history
        $userOrders = $alert->user ? $alert->user->orders()
            ->with('items')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get() : collect();
        
        // Get user's previous alerts
        $previousAlerts = FraudAlert::where('user_id', $alert->user_id)
            ->where('id', '!=', $alert->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        return view('admin.fraud.show-alert', compact('alert', 'userOrders', 'previousAlerts'));
    }

    public function resolveAlert(Request $request, FraudAlert $alert)
    {
        $request->validate([
            'status' => 'required|in:confirmed,false_positive',
            'notes' => 'nullable|string',
        ]);
        
        $alert->resolve(auth()->id(), $request->status, $request->notes);
        
        if ($request->status === 'confirmed' && $request->input('block_user')) {
            $this->fraudService->blockUser($alert->user, 'حظر بسبب تأكيد الاحتيال');
        }
        
        return back()->with('success', 'تم تحديث حالة التنبيه');
    }

    public function rules()
    {
        $rules = FraudRule::orderBy('rule_type')->get();
        
        return view('admin.fraud.rules', compact('rules'));
    }

    public function createRule()
    {
        return view('admin.fraud.create-rule');
    }

    public function storeRule(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'rule_type' => 'required|in:velocity,amount,behavior,location,device',
            'conditions' => 'required|array',
            'risk_weight' => 'required|numeric|min:0|max:1',
            'action' => 'required|in:flag,block,review',
        ]);
        
        FraudRule::create($request->all());
        
        return redirect()->route('admin.fraud.rules')
            ->with('success', 'تم إنشاء القاعدة بنجاح');
    }

    public function toggleRule(FraudRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);
        
        return back()->with('success', 'تم تحديث حالة القاعدة');
    }

    public function deleteRule(FraudRule $rule)
    {
        $rule->delete();
        
        return back()->with('success', 'تم حذف القاعدة');
    }
}
