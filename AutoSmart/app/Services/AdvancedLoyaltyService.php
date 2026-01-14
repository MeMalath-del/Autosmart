<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\User;

class AdvancedLoyaltyService
{
    protected $tiers = [
        'bronze' => ['min' => 0, 'max' => 999, 'multiplier' => 1.0, 'benefits' => ['free_shipping_min' => 200]],
        'silver' => ['min' => 1000, 'max' => 4999, 'multiplier' => 1.25, 'benefits' => ['free_shipping_min' => 100, 'exclusive_sales' => true]],
        'gold' => ['min' => 5000, 'max' => 14999, 'multiplier' => 1.5, 'benefits' => ['free_shipping_min' => 50, 'exclusive_sales' => true, 'priority_support' => true]],
        'platinum' => ['min' => 15000, 'max' => PHP_INT_MAX, 'multiplier' => 2.0, 'benefits' => ['free_shipping_min' => 0, 'exclusive_sales' => true, 'priority_support' => true, 'personal_manager' => true]],
    ];

    protected $earningActions = [
        'order' => ['points_per_sar' => 1, 'description' => 'نقاط من الشراء'],
        'review' => ['points' => 50, 'description' => 'تقييم منتج'],
        'review_photo' => ['points' => 30, 'description' => 'صورة مع التقييم'],
        'referral' => ['points' => 200, 'description' => 'دعوة صديق'],
        'birthday' => ['points' => 100, 'description' => 'هدية عيد الميلاد'],
        'social_share' => ['points' => 25, 'description' => 'مشاركة على السوشيال'],
        'profile_complete' => ['points' => 50, 'description' => 'إكمال الملف الشخصي'],
        'newsletter' => ['points' => 25, 'description' => 'الاشتراك في النشرة'],
    ];

    public function getUserTier(User $user): array
    {
        $totalPoints = $user->loyaltyPoints?->total_points ?? 0;

        foreach ($this->tiers as $name => $tier) {
            if ($totalPoints >= $tier['min'] && $totalPoints <= $tier['max']) {
                return array_merge(['name' => $name], $tier);
            }
        }

        return array_merge(['name' => 'bronze'], $this->tiers['bronze']);
    }

    public function earnPointsFromOrder(Order $order): int
    {
        $user = $order->user;
        $tier = $this->getUserTier($user);

        $basePoints = floor($order->total);
        $earnedPoints = (int) ($basePoints * $tier['multiplier']);

        $this->addPoints($user, $earnedPoints, 'order', $order->id, 'نقاط من الطلب #'.$order->order_number);

        return $earnedPoints;
    }

    public function earnPointsFromAction(User $user, string $action, ?int $referenceId = null): int
    {
        $config = $this->earningActions[$action] ?? null;
        if (! $config) {
            return 0;
        }

        $points = $config['points'] ?? 0;
        $tier = $this->getUserTier($user);
        $earnedPoints = (int) ($points * $tier['multiplier']);

        $this->addPoints($user, $earnedPoints, $action, $referenceId, $config['description']);

        return $earnedPoints;
    }

    public function addPoints(User $user, int $points, string $type, ?int $referenceId, string $description): void
    {
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'type' => 'earn',
            'points' => $points,
            'action' => $type,
            'reference_id' => $referenceId,
            'description' => $description,
        ]);

        $loyaltyPoints = $user->loyaltyPoints ?? LoyaltyPoint::create(['user_id' => $user->id]);
        $loyaltyPoints->increment('total_points', $points);
        $loyaltyPoints->increment('available_points', $points);
    }

    public function redeemPoints(User $user, int $points, ?Order $order = null): bool
    {
        $loyaltyPoints = $user->loyaltyPoints;
        if (! $loyaltyPoints || $loyaltyPoints->available_points < $points) {
            return false;
        }

        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'type' => 'redeem',
            'points' => -$points,
            'action' => 'order_discount',
            'reference_id' => $order?->id,
            'description' => 'استبدال نقاط',
        ]);

        $loyaltyPoints->decrement('available_points', $points);
        $loyaltyPoints->increment('redeemed_points', $points);

        return true;
    }

    public function getPointsValue(int $points): float
    {
        return $points * 0.1; // 1 point = 0.1 SAR
    }

    public function getAvailableRedemptions(User $user): array
    {
        $points = $user->loyaltyPoints?->available_points ?? 0;

        return [
            ['points' => 100, 'value' => 10, 'available' => $points >= 100],
            ['points' => 250, 'value' => 25, 'available' => $points >= 250],
            ['points' => 500, 'value' => 50, 'available' => $points >= 500],
            ['points' => 1000, 'value' => 100, 'available' => $points >= 1000],
        ];
    }

    public function getEarningActions(): array
    {
        return $this->earningActions;
    }

    public function getTiers(): array
    {
        return $this->tiers;
    }
}
