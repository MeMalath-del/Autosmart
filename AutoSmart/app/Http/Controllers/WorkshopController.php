<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceQuote;
use App\Models\MaintenanceBooking;
use App\Models\UserCar;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index(Request $request)
    {
        $query = Workshop::approved()->withCount('services');

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('specialty')) {
            $query->whereJsonContains('specialties', $request->specialty);
        }
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('name_ar', 'like', "%{$request->search}%"));
        }

        $workshops = $query->orderByDesc('rating')->paginate(12);
        $cities = Workshop::approved()->distinct()->pluck('city');

        return view('workshops.index', compact('workshops', 'cities'));
    }

    public function show(Workshop $workshop)
    {
        if (!$workshop->isApproved()) abort(404);
        $workshop->load(['services', 'reviews.user']);
        return view('workshops.show', compact('workshop'));
    }

    public function maintenanceRequests()
    {
        $this->middleware('auth');
        $requests = MaintenanceRequest::where('user_id', auth()->id())
            ->with(['userCar', 'quotes.workshop'])
            ->withCount('quotes')
            ->latest()
            ->paginate(10);
        return view('workshops.requests.index', compact('requests'));
    }

    public function createRequest()
    {
        $this->middleware('auth');
        $cars = UserCar::where('user_id', auth()->id())->with(['brand', 'model'])->get();
        return view('workshops.requests.create', compact('cars'));
    }

    public function storeRequest(Request $request)
    {
        $this->middleware('auth');
        
        $validated = $request->validate([
            'user_car_id' => 'nullable|exists:user_cars,id',
            'car_info' => 'nullable|string|max:200',
            'issue_description' => 'required|string|max:2000',
            'urgency' => 'required|in:low,medium,high',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time' => 'nullable|string',
            'city' => 'required|string|max:100',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('maintenance-requests', 'public');
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['images'] = $images ?: null;

        MaintenanceRequest::create($validated);

        return redirect()->route('workshops.my-requests')
            ->with('success', 'تم إرسال طلب الصيانة. ستتلقى عروضاً من الورش قريباً');
    }

    public function showRequest(MaintenanceRequest $maintenanceRequest)
    {
        if ($maintenanceRequest->user_id !== auth()->id()) abort(403);
        $maintenanceRequest->load(['userCar.brand', 'userCar.model', 'quotes.workshop']);
        return view('workshops.requests.show', compact('maintenanceRequest'));
    }

    public function acceptQuote(MaintenanceQuote $quote)
    {
        if ($quote->maintenanceRequest->user_id !== auth()->id()) abort(403);
        
        $quote->accept();
        $quote->maintenanceRequest->update(['status' => 'booked']);

        // إنشاء حجز
        MaintenanceBooking::create([
            'maintenance_request_id' => $quote->maintenance_request_id,
            'maintenance_quote_id' => $quote->id,
            'user_id' => auth()->id(),
            'workshop_id' => $quote->workshop_id,
            'booking_date' => $quote->available_date ?? now()->addDays(1),
            'booking_time' => '09:00',
            'estimated_cost' => $quote->total,
        ]);

        return back()->with('success', 'تم قبول العرض وإنشاء الحجز');
    }

    public function bookings()
    {
        $bookings = MaintenanceBooking::where('user_id', auth()->id())
            ->with(['workshop', 'service'])
            ->latest()
            ->paginate(10);
        return view('workshops.bookings.index', compact('bookings'));
    }
}
