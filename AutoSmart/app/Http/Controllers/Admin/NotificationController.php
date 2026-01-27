<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationCampaign;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'role:admin']); }

    public function index()
    {
        $campaigns = NotificationCampaign::latest()->paginate(20);
        return view('admin.notifications.index', compact('campaigns'));
    }

    public function create()
    {
        $segments = ['all' => 'جميع المستخدمين', 'customers' => 'العملاء', 'sellers' => 'البائعين', 'inactive' => 'غير نشطين'];
        return view('admin.notifications.create', compact('segments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
            'action_url' => 'nullable|url',
            'target_segments' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['image'] = $request->hasFile('image') ? $request->file('image')->store('notifications', 'public') : null;
        $validated['status'] = $validated['scheduled_at'] ? 'scheduled' : 'draft';

        $campaign = NotificationCampaign::create($validated);

        if (!$validated['scheduled_at']) {
            return redirect()->route('admin.notifications.show', $campaign);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم جدولة الحملة');
    }

    public function show(NotificationCampaign $notification)
    {
        $notification->load('logs');
        return view('admin.notifications.show', compact('notification'));
    }

    public function send(NotificationCampaign $notification)
    {
        $segments = $notification->target_segments ?? ['all'];
        
        $query = User::query();
        if (!in_array('all', $segments)) {
            if (in_array('customers', $segments)) $query->where('role', 'customer');
            if (in_array('sellers', $segments)) $query->where('role', 'seller');
        }

        $users = $query->get();
        $notification->update(['total_recipients' => $users->count(), 'status' => 'sending']);

        foreach ($users as $user) {
            $notification->logs()->create([
                'user_id' => $user->id,
                'title' => $notification->title,
                'body' => $notification->body,
            ]);
        }

        $notification->update(['status' => 'sent', 'sent_at' => now()]);

        return back()->with('success', 'تم إرسال الإشعارات');
    }
}
