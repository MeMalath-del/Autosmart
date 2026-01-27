<?php

namespace App\Http\Controllers;

use App\Models\InstallationService;
use App\Models\InstallationBooking;
use App\Models\Order;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function bookings()
    {
        $bookings = InstallationBooking::where('user_id', auth()->id())
            ->with(['service', 'workshop'])
            ->latest()
            ->paginate(10);
        return view('installation.bookings', compact('bookings'));
    }

    public function book(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        
        $order->load(['items.product.installationServices.workshop']);
        return view('installation.book', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        
        $validated = $request->validate([
            'service_id' => 'required|exists:installation_services,id',
            'workshop_id' => 'required|exists:workshops,id',
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required',
            'notes' => 'nullable|string|max:500',
            'user_car_details' => 'nullable|string|max:500',
        ]);

        $booking = InstallationBooking::create([
            'order_id' => $order->id,
            'service_id' => $validated['service_id'],
            'workshop_id' => $validated['workshop_id'],
            'user_id' => auth()->id(),
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'notes' => $validated['notes'],
            'user_car_details' => $validated['user_car_details'],
        ]);

        return redirect()->route('installation.bookings')
            ->with('success', 'تم حجز خدمة التركيب');
    }

    public function show(InstallationBooking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        $booking->load(['service', 'workshop', 'order']);
        return view('installation.show', compact('booking'));
    }

    public function cancel(InstallationBooking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'لا يمكن إلغاء هذا الحجز');
        }
        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'تم إلغاء الحجز');
    }
}
