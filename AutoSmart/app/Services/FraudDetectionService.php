<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\FraudAlert;
use App\Models\FraudRule;

class FraudDetectionService
{
    public function analyzeOrder(Order $order): ?FraudAlert
    {
        $riskFactors = [];
        $totalRisk = 0;
        
        $rules = FraudRule::active()->get();
        
        foreach ($rules as $rule) {
            $ruleResult = $this->evaluateRule($rule, $order);
            if ($ruleResult['triggered']) {
                $riskFactors[] = [
                    'rule' => $rule->name,
                    'type' => $rule->rule_type,
                    'weight' => $rule->risk_weight,
                    'details' => $ruleResult['details'],
                ];
                $totalRisk += $rule->risk_weight;
            }
        }
        
        // Additional built-in checks
        $builtInChecks = $this->performBuiltInChecks($order);
        $riskFactors = array_merge($riskFactors, $builtInChecks['factors']);
        $totalRisk += $builtInChecks['risk'];
        
        // Normalize risk score (0-1)
        $riskScore = min(1, $totalRisk);
        
        // Create alert if risk is significant
        if ($riskScore >= 0.3) {
            return FraudAlert::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'alert_type' => $this->determineAlertType($riskFactors),
                'risk_score' => $riskScore,
                'risk_factors' => $riskFactors,
                'status' => 'new',
            ]);
        }
        
        return null;
    }

    protected function evaluateRule(FraudRule $rule, Order $order): array
    {
        $conditions = $rule->conditions ?? [];
        $triggered = false;
        $details = [];
        
        switch ($rule->rule_type) {
            case 'velocity':
                $result = $this->checkVelocity($order, $conditions);
                break;
            case 'amount':
                $result = $this->checkAmount($order, $conditions);
                break;
            case 'behavior':
                $result = $this->checkBehavior($order, $conditions);
                break;
            default:
                $result = ['triggered' => false, 'details' => []];
        }
        
        return $result;
    }

    protected function checkVelocity(Order $order, array $conditions): array
    {
        $timeWindow = $conditions['time_window_minutes'] ?? 60;
        $maxOrders = $conditions['max_orders'] ?? 5;
        
        $recentOrders = Order::where('user_id', $order->user_id)
            ->where('created_at', '>=', now()->subMinutes($timeWindow))
            ->count();
        
        if ($recentOrders > $maxOrders) {
            return [
                'triggered' => true,
                'details' => [
                    'message' => "عدد الطلبات في آخر {$timeWindow} دقيقة: {$recentOrders}",
                    'threshold' => $maxOrders,
                    'actual' => $recentOrders,
                ],
            ];
        }
        
        return ['triggered' => false, 'details' => []];
    }

    protected function checkAmount(Order $order, array $conditions): array
    {
        $maxAmount = $conditions['max_amount'] ?? 10000;
        
        if ($order->total > $maxAmount) {
            return [
                'triggered' => true,
                'details' => [
                    'message' => "قيمة الطلب ({$order->total}) تتجاوز الحد المسموح",
                    'threshold' => $maxAmount,
                    'actual' => $order->total,
                ],
            ];
        }
        
        return ['triggered' => false, 'details' => []];
    }

    protected function checkBehavior(Order $order, array $conditions): array
    {
        // Check for suspicious patterns
        $user = $order->user;
        $triggered = false;
        $details = [];
        
        // New account with high-value order
        if ($user->created_at > now()->subDays(7) && $order->total > 1000) {
            $triggered = true;
            $details['new_account_high_value'] = true;
        }
        
        // Multiple shipping addresses
        $addressCount = $user->addresses()->count();
        if ($addressCount > 5) {
            $triggered = true;
            $details['multiple_addresses'] = $addressCount;
        }
        
        return [
            'triggered' => $triggered,
            'details' => $details,
        ];
    }

    protected function performBuiltInChecks(Order $order): array
    {
        $factors = [];
        $risk = 0;
        
        // Check 1: IP geolocation mismatch
        // (Simplified - in production use IP geolocation service)
        
        // Check 2: Multiple payment methods attempted
        $failedPayments = 0; // Would check payment gateway logs
        if ($failedPayments >= 3) {
            $factors[] = [
                'rule' => 'محاولات دفع فاشلة متعددة',
                'type' => 'payment',
                'weight' => 0.3,
            ];
            $risk += 0.3;
        }
        
        // Check 3: Email domain check
        $user = $order->user;
        $email = $user->email;
        $disposableDomains = ['tempmail.com', 'throwaway.com', '10minutemail.com'];
        $domain = substr($email, strpos($email, '@') + 1);
        
        if (in_array($domain, $disposableDomains)) {
            $factors[] = [
                'rule' => 'بريد إلكتروني مؤقت',
                'type' => 'email',
                'weight' => 0.25,
            ];
            $risk += 0.25;
        }
        
        return [
            'factors' => $factors,
            'risk' => $risk,
        ];
    }

    protected function determineAlertType(array $riskFactors): string
    {
        if (empty($riskFactors)) return 'pattern_match';
        
        $types = array_column($riskFactors, 'type');
        
        if (in_array('velocity', $types)) return 'velocity_check';
        if (in_array('amount', $types)) return 'high_value';
        if (in_array('payment', $types)) return 'suspicious_payment';
        
        return 'pattern_match';
    }

    public function blockUser(User $user, string $reason): void
    {
        $user->update(['is_blocked' => true]);
        
        // Log the action
        FraudAlert::where('user_id', $user->id)
            ->where('status', 'new')
            ->update(['status' => 'confirmed', 'notes' => $reason]);
    }
}
