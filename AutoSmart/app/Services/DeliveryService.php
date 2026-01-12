<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ExpressDeliveryZone;
use App\Models\ExpressDelivery;
use App\Models\SmartLocker;
use App\Models\LockerReservation;
use App\Models\OrderBundle;
use App\Models\InternationalShippingZone;
use App\Models\InternationalShipment;

class DeliveryService
{
    public function getExpressDeliveryOptions(string $city, ?string $district = null): array
    {
        $zone = ExpressDeliveryZone::active()
            ->forCity($city)
            ->first();
        
        if (!$zone) {
            return ['available' => false];
        }
        
        $options = [];
        
        // Same day delivery
        if ($zone->canAcceptSameDayOrder()) {
            $options['same_day'] = [
                'type' => 'same_day',
                'name' => 'توصيل في نفس اليوم',
                'fee' => $zone->same_day_fee,
                'estimated_time' => 'اليوم قبل الساعة 9 مساءً',
            ];
        }
        
        // Express delivery (2-4 hours)
        $options['express'] = [
            'type' => 'express',
            'name' => 'توصيل سريع',
            'fee' => $zone->express_fee,
            'estimated_time' => '2-4 ساعات',
        ];
        
        return [
            'available' => true,
            'zone_id' => $zone->id,
            'options' => $options,
        ];
    }

    public function createExpressDelivery(Order $order, string $type): ExpressDelivery
    {
        $address = $order->address;
        $zone = ExpressDeliveryZone::active()
            ->forCity($address->city)
            ->firstOrFail();
        
        $promisedTime = match($type) {
            'same_day' => now()->setTime(21, 0),
            'express' => now()->addHours(4),
            default => now()->addHours(24),
        };
        
        return ExpressDelivery::create([
            'order_id' => $order->id,
            'zone_id' => $zone->id,
            'type' => $type,
            'fee' => $zone->getDeliveryFee($type),
            'promised_delivery_time' => $promisedTime,
            'status' => 'pending',
        ]);
    }

    public function getNearbyLockers(string $city, float $lat = null, float $lng = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = SmartLocker::active()
            ->inCity($city)
            ->where('available_compartments', '>', 0);
        
        if ($lat && $lng) {
            // Sort by distance (simplified - in production use proper geo query)
            $query->orderByRaw("ABS(latitude - ?) + ABS(longitude - ?)", [$lat, $lng]);
        }
        
        return $query->limit(10)->get();
    }

    public function reserveLocker(Order $order, SmartLocker $locker, string $size = 'medium'): LockerReservation
    {
        return $locker->reserveCompartment($order, $size);
    }

    public function createOrderBundle($user, int $hoursToWait = 48): OrderBundle
    {
        return OrderBundle::create([
            'user_id' => $user->id,
            'original_shipping_total' => 0,
            'bundled_shipping_cost' => 0,
            'savings' => 0,
            'bundle_deadline' => now()->addHours($hoursToWait),
            'status' => 'open',
        ]);
    }

    public function addOrderToBundle(Order $order, OrderBundle $bundle, float $originalShipping): void
    {
        if (!$bundle->isOpen()) {
            throw new \Exception('التجميع مغلق');
        }
        
        $bundle->addOrder($order, $originalShipping);
    }

    public function closeBundle(OrderBundle $bundle): void
    {
        $bundle->close();
    }

    public function getInternationalShippingCost(string $countryCode, float $weight): ?array
    {
        $zone = InternationalShippingZone::active()
            ->where('country_code', $countryCode)
            ->first();
        
        if (!$zone) {
            return null;
        }
        
        return [
            'zone_id' => $zone->id,
            'country' => $zone->localized_name,
            'shipping_cost' => $zone->calculateShippingCost($weight),
            'estimated_delivery' => $zone->estimated_delivery,
            'restricted_items' => $zone->restricted_items,
        ];
    }

    public function createInternationalShipment(Order $order, InternationalShippingZone $zone, float $weight): InternationalShipment
    {
        return InternationalShipment::create([
            'order_id' => $order->id,
            'zone_id' => $zone->id,
            'weight' => $weight,
            'shipping_cost' => $zone->calculateShippingCost($weight),
            'customs_fee' => 0, // Calculated by customs
            'status' => 'pending',
        ]);
    }

    public function calculateShippingOptions(Order $order): array
    {
        $options = [];
        $address = $order->address;
        
        // Standard shipping
        $options['standard'] = [
            'type' => 'standard',
            'name' => 'توصيل عادي',
            'fee' => 25,
            'estimated_days' => '3-5 أيام عمل',
        ];
        
        // Express options
        $expressOptions = $this->getExpressDeliveryOptions($address->city ?? '');
        if ($expressOptions['available']) {
            $options = array_merge($options, $expressOptions['options']);
        }
        
        // Locker pickup
        $lockers = $this->getNearbyLockers($address->city ?? '');
        if ($lockers->count() > 0) {
            $options['locker'] = [
                'type' => 'locker',
                'name' => 'استلام من خزانة ذكية',
                'fee' => 15,
                'estimated_days' => '2-3 أيام عمل',
                'lockers_count' => $lockers->count(),
            ];
        }
        
        return $options;
    }
}
