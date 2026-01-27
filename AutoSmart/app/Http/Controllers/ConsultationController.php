<?php

namespace App\Http\Controllers;

use App\Models\ConsultationExpert;
use App\Models\ConsultationSession;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $experts = ConsultationExpert::where('is_active', true)->with('user')->get();
        $mySessions = ConsultationSession::where('user_id', auth()->id())
            ->with('expert')
            ->latest()
            ->get();

        return view('consultations.index', compact('experts', 'mySessions'));
    }

    public function book(ConsultationExpert $expert)
    {
        $expert->load('user');

        return view('consultations.book', compact('expert'));
    }

    public function store(Request $request, ConsultationExpert $expert)
    {
        $validated = $request->validate([
            'type' => 'required|in:phone,video,chat',
            'topic' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|in:30,60,90',
        ]);

        $price = ($validated['duration_minutes'] / 60) * $expert->hourly_rate;

        $session = ConsultationSession::create([
            'user_id' => auth()->id(),
            'expert_id' => $expert->user_id,
            'type' => $validated['type'],
            'topic' => $validated['topic'],
            'description' => $validated['description'],
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'price' => $price,
        ]);

        return redirect()->route('consultations.show', $session)
            ->with('success', 'تم حجز الجلسة');
    }

    public function show(ConsultationSession $session)
    {
        if ($session->user_id !== auth()->id() && $session->expert_id !== auth()->id()) {
            abort(403);
        }
        $session->load(['user', 'expert']);

        return view('consultations.show', compact('session'));
    }

    public function rate(Request $request, ConsultationSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $session->update($validated);

        // Update expert rating
        $expert = ConsultationExpert::where('user_id', $session->expert_id)->first();
        if ($expert) {
            $avgRating = ConsultationSession::where('expert_id', $session->expert_id)
                ->whereNotNull('rating')
                ->avg('rating');
            $expert->update(['rating' => $avgRating]);
        }

        return back()->with('success', 'شكراً لتقييمك');
    }
}
