<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceBooking;
use App\Models\MaintenanceQuote;
use App\Models\MaintenanceRequest;
use App\Models\Workshop;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $workshop = auth()->user()->workshop;
        if (! $workshop) {
            return redirect()->route('workshop.create');
        }

        $stats = [
            'pending_quotes' => MaintenanceQuote::where('workshop_id', $workshop->id)->where('status', 'pending')->count(),
            'active_bookings' => MaintenanceBooking::where('workshop_id', $workshop->id)->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
            'completed_this_month' => MaintenanceBooking::where('workshop_id', $workshop->id)->where('status', 'completed')->whereMonth('created_at', now()->month)->count(),
            'total_revenue' => MaintenanceBooking::where('workshop_id', $workshop->id)->where('status', 'completed')->sum('final_cost'),
        ];

        $recentBookings = MaintenanceBooking::where('workshop_id', $workshop->id)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $openRequests = MaintenanceRequest::open()
            ->where('city', $workshop->city)
            ->withCount('quotes')
            ->latest()
            ->take(10)
            ->get();

        return view('workshop.dashboard', compact('workshop', 'stats', 'recentBookings', 'openRequests'));
    }

    public function create()
    {
        if (auth()->user()->workshop) {
            return redirect()->route('workshop.dashboard');
        }

        return view('workshop.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'specialties' => 'nullable|array',
        ]);

        $validated['user_id'] = auth()->id();
        Workshop::create($validated);

        return redirect()->route('workshop.pending')
            ->with('success', 'تم تقديم طلب تسجيل الورشة. سيتم مراجعته قريباً');
    }

    public function pending()
    {
        $workshop = auth()->user()->workshop;
        if (! $workshop) {
            return redirect()->route('workshop.create');
        }
        if ($workshop->isApproved()) {
            return redirect()->route('workshop.dashboard');
        }

        return view('workshop.pending', compact('workshop'));
    }

    public function requests()
    {
        $workshop = auth()->user()->workshop;

        $requests = MaintenanceRequest::open()
            ->where('city', $workshop->city)
            ->with(['user', 'userCar.brand', 'userCar.model'])
            ->withCount('quotes')
            ->latest()
            ->paginate(20);

        return view('workshop.requests.index', compact('requests'));
    }

    public function submitQuote(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $workshop = auth()->user()->workshop;

        $validated = $request->validate([
            'labor_cost' => 'required|numeric|min:0',
            'parts_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'estimated_hours' => 'nullable|integer|min:1',
            'available_date' => 'nullable|date|after:today',
        ]);

        $validated['workshop_id'] = $workshop->id;
        $validated['maintenance_request_id'] = $maintenanceRequest->id;
        $validated['total'] = $validated['labor_cost'] + ($validated['parts_cost'] ?? 0);
        $validated['expires_at'] = now()->addDays(3);

        MaintenanceQuote::create($validated);

        if ($maintenanceRequest->status === 'open') {
            $maintenanceRequest->update(['status' => 'quoted']);
        }

        return back()->with('success', 'تم إرسال عرض السعر');
    }

    public function bookings(Request $request)
    {
        $workshop = auth()->user()->workshop;

        $query = MaintenanceBooking::where('workshop_id', $workshop->id)->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(20);

        return view('workshop.bookings.index', compact('bookings'));
    }

    public function updateBooking(Request $request, MaintenanceBooking $booking)
    {
        if ($booking->workshop_id !== auth()->user()->workshop->id) {
            abort(403);
        }

        $booking->update($request->validate([
            'status' => 'required|in:confirmed,in_progress,completed,cancelled',
            'final_cost' => 'nullable|numeric|min:0',
        ]));

        return back()->with('success', 'تم تحديث الحجز');
    }
}
