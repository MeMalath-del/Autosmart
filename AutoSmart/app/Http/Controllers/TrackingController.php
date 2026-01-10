<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DeliveryTracking;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        
        $tracking = DeliveryTracking::where('order_id', $order->id)
            ->with('driver')
            ->latest()
            ->get();
        
        $currentLocation = $tracking->first();
        
        return view('tracking.live', compact('order', 'tracking', 'currentLocation'));
    }

    public function getLocation(Order $order)
    {
        $tracking = DeliveryTracking::where('order_id', $order->id)
            ->with('driver')
            ->latest()
            ->first();
        
        return response()->json([
            'lat' => $tracking?->latitude,
            'lng' => $tracking?->longitude,
            'status' => $tracking?->status,
            'estimated_minutes' => $tracking?->estimated_minutes,
            'driver' => $tracking?->driver ? [
                'name' => $tracking->driver->name,
                'phone' => $tracking->driver->phone,
                'rating' => $tracking->driver->rating,
            ] : null
        ]);
    }

    public function history(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        
        $history = DeliveryTracking::where('order_id', $order->id)->get();
        
        return view('tracking.history', compact('order', 'history'));
    }
}
