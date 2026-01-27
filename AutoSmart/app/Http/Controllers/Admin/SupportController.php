<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\KnowledgeBase;
use App\Models\Faq;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'role:admin']); }

    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(20);
        
        $stats = [
            'open' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->where('status', '!=', 'closed')->count(),
            'unassigned' => SupportTicket::whereNull('assigned_to')->where('status', 'open')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'order', 'replies.user']);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate(['message' => 'required|string|max:5000']);
        
        $ticket->addReply(auth()->user(), $request->message, $request->boolean('is_internal'));
        
        if ($request->filled('status')) {
            $ticket->update(['status' => $request->status]);
        }

        return back()->with('success', 'تم إرسال الرد');
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress'
        ]);
        return back()->with('success', 'تم تعيين التذكرة');
    }

    public function faqs()
    {
        $faqs = Faq::orderBy('sort_order')->get();
        return view('admin.support.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        Faq::create($validated);
        return back()->with('success', 'تمت إضافة السؤال');
    }

    public function knowledgeBase()
    {
        $articles = KnowledgeBase::orderBy('sort_order')->get();
        return view('admin.support.knowledge-base', compact('articles'));
    }

    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|array',
        ]);

        KnowledgeBase::create($validated);
        return back()->with('success', 'تمت إضافة المقال');
    }
}
