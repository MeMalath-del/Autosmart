<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use Illuminate\Http\Request;

class InfluencerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $influencer = Influencer::where('user_id', auth()->id())->first();

        if (! $influencer) {
            return view('influencer.apply');
        }

        if ($influencer->status === 'pending') {
            return view('influencer.pending', compact('influencer'));
        }

        $sales = $influencer->sales()->with('order')->latest()->paginate(20);

        return view('influencer.dashboard', compact('influencer', 'sales'));
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'required|string|max:1000',
            'youtube' => 'nullable|url',
            'instagram' => 'nullable|string|max:100',
            'twitter' => 'nullable|string|max:100',
            'tiktok' => 'nullable|string|max:100',
        ]);

        $validated['user_id'] = auth()->id();
        Influencer::create($validated);

        return redirect()->route('influencer.index')
            ->with('success', 'تم تقديم طلبك. سنراجعه قريباً');
    }

    public function link(string $code)
    {
        $influencer = Influencer::where('code', strtoupper($code))->approved()->first();

        if (! $influencer) {
            abort(404);
        }

        session(['influencer_code' => $influencer->code]);

        return redirect()->route('home')
            ->with('success', 'مرحباً! أنت تتصفح عبر رابط '.$influencer->user->name);
    }
}
