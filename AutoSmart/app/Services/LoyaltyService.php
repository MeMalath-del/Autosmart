<?php

namespace App\Services;

use App\Models\LoyaltyPoints;
use App\Models\Order;
use App\Models\Referral;

class LoyaltyService
{
    protected int $pointsPerSar = 1;

    protected float $sarPerPoint = 0.01;

    public function earnPointsFromOrder(Order $order): int
    {
        $loyalty = LoyaltyPoints::getOrCreate($order->user_id);
        $multiplier = $this->getTierMultiplier($loyalty->tier);

        $points = (int) ($order->total * $this->pointsPerSar * $multiplier);
        $loyalty->addPoints($points, 'طلب #'.$order->order_number, 'order', $order->id);

        // Check if user was referred and qualify referral
        $this->qualifyReferral($order->user_id);

        return $points;
    }

    public function getTierMultiplier(string $tier): float
    {
        return match ($tier) {
            'platinum' => 3.0,
            'gold' => 2.0,
            'silver' => 1.5,
            default => 1.0,
        };
    }

    public function redeemPoints(int $userId, int $points): float
    {
        $loyalty = LoyaltyPoints::getOrCreate($userId);

        if ($loyalty->redeemPoints($points, 'استبدال نقاط')) {
            return $points * $this->sarPerPoint;
        }

        return 0;
    }

    public function getPointsValue(int $points): float
    {
        return $points * $this->sarPerPoint;
    }

    protected function qualifyReferral(int $userId): void
    {
        $referral = Referral::where('referred_id', $userId)
            ->where('status', 'pending')
            ->first();

        if ($referral) {
            $referral->qualify();
            $referral->reward();
        }
    }

    public function addBonusPoints(int $userId, int $points, string $reason): void
    {
        $loyalty = LoyaltyPoints::getOrCreate($userId);
        $loyalty->addPoints($points, $reason);
    }
}
