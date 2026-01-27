<?php

namespace App\Http\Controllers;

use App\Models\PartLifecycle;
use App\Models\UserCar;
use Illuminate\Http\Request;

class PartLifecycleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $parts = PartLifecycle::where('user_id', auth()->id())
            ->with(['product', 'userCar.carModel.brand'])
            ->latest()
            ->paginate(20);

        $expiring = PartLifecycle::where('user_id', auth()->id())
            ->whereIn('status', ['warning', 'expired'])
            ->count();

        return view('parts.index', compact('parts', 'expiring'));
    }

    public function create()
    {
        $cars = UserCar::where('user_id', auth()->id())->with('carModel.brand')->get();

        return view('parts.create', compact('cars'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_car_id' => 'required|exists:user_cars,id',
            'product_id' => 'required|exists:products,id',
            'installation_date' => 'required|date|before_or_equal:today',
            'installation_mileage' => 'nullable|integer|min:0',
            'expected_lifespan_km' => 'nullable|integer|min:0',
            'expected_lifespan_months' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = auth()->id();

        // Calculate expected replacement
        if ($validated['expected_lifespan_months']) {
            $validated['expected_replacement_date'] = now()->parse($validated['installation_date'])->addMonths($validated['expected_lifespan_months']);
        }
        if ($validated['installation_mileage'] && $validated['expected_lifespan_km']) {
            $validated['expected_replacement_mileage'] = $validated['installation_mileage'] + $validated['expected_lifespan_km'];
        }

        PartLifecycle::create($validated);

        return redirect()->route('parts.index')
            ->with('success', 'تم تسجيل القطعة');
    }

    public function update(Request $request, PartLifecycle $part)
    {
        if ($part->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'actual_replacement_date' => 'nullable|date',
            'actual_replacement_mileage' => 'nullable|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['actual_replacement_date']) {
            $validated['status'] = 'replaced';
        }

        $part->update($validated);

        return back()->with('success', 'تم التحديث');
    }
}
