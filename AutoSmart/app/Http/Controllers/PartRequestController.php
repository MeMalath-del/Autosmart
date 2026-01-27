<?php

namespace App\Http\Controllers;

use App\Models\PartRequest;
use App\Models\PartRequestQuote;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Http\Request;

class PartRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $requests = PartRequest::where('user_id', auth()->id())
            ->with(['carBrand', 'carModel', 'quotes.store'])
            ->withCount('quotes')
            ->latest()
            ->paginate(10);

        return view('part-requests.index', compact('requests'));
    }

    public function create()
    {
        $carBrands = CarBrand::active()->with('models')->get();
        return view('part-requests.create', compact('carBrands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_brand_id' => 'nullable|exists:car_brands,id',
            'car_model_id' => 'nullable|exists:car_models,id',
            'car_year' => 'nullable|integer|min:1900|max:2030',
            'part_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'part_number' => 'nullable|string|max:100',
            'urgency' => 'required|in:low,medium,high',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('part-requests', 'public');
            }
        }

        PartRequest::create([
            'user_id' => auth()->id(),
            'car_brand_id' => $validated['car_brand_id'],
            'car_model_id' => $validated['car_model_id'],
            'car_year' => $validated['car_year'],
            'part_name' => $validated['part_name'],
            'description' => $validated['description'],
            'part_number' => $validated['part_number'],
            'urgency' => $validated['urgency'],
            'budget_min' => $validated['budget_min'],
            'budget_max' => $validated['budget_max'],
            'images' => !empty($images) ? $images : null,
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('part-requests.index')
            ->with('success', 'تم نشر طلبك. سيتواصل معك البائعون قريباً');
    }

    public function show(PartRequest $partRequest)
    {
        if ($partRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $partRequest->load(['carBrand', 'carModel', 'quotes.store']);

        return view('part-requests.show', compact('partRequest'));
    }

    public function acceptQuote(PartRequestQuote $quote)
    {
        if ($quote->partRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $quote->accept();

        return back()->with('success', 'تم قبول العرض');
    }

    public function rejectQuote(PartRequestQuote $quote)
    {
        if ($quote->partRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $quote->reject();

        return back()->with('success', 'تم رفض العرض');
    }

    public function close(PartRequest $partRequest)
    {
        if ($partRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $partRequest->update(['status' => 'closed']);

        return back()->with('success', 'تم إغلاق الطلب');
    }
}
