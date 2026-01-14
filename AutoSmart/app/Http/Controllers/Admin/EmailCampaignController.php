<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Http\Request;

class EmailCampaignController extends Controller
{
    public function index()
    {
        $campaigns = EmailCampaign::latest()->paginate(20);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $segments = [
            'all' => 'جميع المستخدمين',
            'customers' => 'العملاء فقط',
            'sellers' => 'البائعين فقط',
            'inactive' => 'غير النشطين (30 يوم)',
        ];

        return view('admin.campaigns.create', compact('segments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'subject' => 'required|string|max:200',
            'body' => 'required|string',
            'segment' => 'required|in:all,customers,sellers,inactive',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['status'] = $validated['scheduled_at'] ? 'scheduled' : 'draft';
        $validated['recipients_count'] = $this->getRecipientsCount($validated['segment']);

        EmailCampaign::create($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'تم إنشاء الحملة');
    }

    public function send(EmailCampaign $emailCampaign)
    {
        if ($emailCampaign->status === 'sent') {
            return back()->with('error', 'تم إرسال الحملة مسبقاً');
        }

        $users = $this->getRecipients($emailCampaign->segment);

        foreach ($users as $user) {
            // In production, use queued jobs
            try {
                \Mail::to($user->email)->send(new \App\Mail\CampaignEmail($emailCampaign, $user));
                $emailCampaign->logs()->create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'status' => 'sent',
                ]);
                $emailCampaign->increment('sent_count');
            } catch (\Exception $e) {
                $emailCampaign->logs()->create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'status' => 'failed',
                ]);
            }
        }

        $emailCampaign->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'تم إرسال الحملة');
    }

    protected function getRecipientsCount(string $segment): int
    {
        return $this->getRecipientsQuery($segment)->count();
    }

    protected function getRecipients(string $segment)
    {
        return $this->getRecipientsQuery($segment)->get();
    }

    protected function getRecipientsQuery(string $segment)
    {
        return match ($segment) {
            'customers' => User::where('role', 'customer'),
            'sellers' => User::where('role', 'seller'),
            'inactive' => User::where('last_login_at', '<', now()->subDays(30)),
            default => User::query(),
        };
    }

    public function destroy(EmailCampaign $emailCampaign)
    {
        $emailCampaign->delete();

        return back()->with('success', 'تم حذف الحملة');
    }
}
