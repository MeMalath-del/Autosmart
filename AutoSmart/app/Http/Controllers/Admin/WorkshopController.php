<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index(Request $request)
    {
        $query = Workshop::with('user')->withCount(['bookings', 'reviews']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workshops = $query->latest()->paginate(20);
        return view('admin.workshops.index', compact('workshops'));
    }

    public function show(Workshop $workshop)
    {
        $workshop->load(['user', 'services', 'reviews']);
        return view('admin.workshops.show', compact('workshop'));
    }

    public function approve(Workshop $workshop)
    {
        $workshop->update(['status' => 'approved']);
        
        // Notify the workshop owner
        $workshop->user->notify(new \App\Notifications\WorkshopApproved($workshop));

        return back()->with('success', 'تمت الموافقة على الورشة');
    }

    public function suspend(Request $request, Workshop $workshop)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $workshop->update(['status' => 'suspended']);
        return back()->with('success', 'تم تعليق الورشة');
    }

    public function toggleFeatured(Workshop $workshop)
    {
        $workshop->update(['is_featured' => !$workshop->is_featured]);
        return back()->with('success', $workshop->is_featured ? 'تم تمييز الورشة' : 'تم إلغاء تمييز الورشة');
    }

    public function toggleVerified(Workshop $workshop)
    {
        $workshop->update(['is_verified' => !$workshop->is_verified]);
        return back()->with('success', $workshop->is_verified ? 'تم توثيق الورشة' : 'تم إلغاء توثيق الورشة');
    }
}
