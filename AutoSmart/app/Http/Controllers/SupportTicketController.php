<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Faq;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        $faqs = Faq::active()->get()->groupBy('category');
        return view('support.create', compact('faqs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:200',
            'category' => 'required|in:order,payment,shipping,product,account,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string|max:5000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'order_id' => $validated['order_id'] ?? null,
        ]);

        $ticket->addReply(auth()->user(), $validated['message']);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'تم إنشاء التذكرة بنجاح');
    }

    public function show(SupportTicket $supportTicket)
    {
        if ($supportTicket->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $supportTicket->load('replies.user');
        return view('support.show', compact('supportTicket'));
    }

    public function reply(Request $request, SupportTicket $supportTicket)
    {
        if ($supportTicket->user_id !== auth()->id()) abort(403);
        
        $request->validate(['message' => 'required|string|max:5000']);
        $supportTicket->addReply(auth()->user(), $request->message);
        $supportTicket->update(['status' => 'open']);

        return back()->with('success', 'تم إرسال ردك');
    }

    public function faq()
    {
        $faqs = Faq::active()->get()->groupBy('category');
        return view('support.faq', compact('faqs'));
    }

    public function knowledgeBase()
    {
        $articles = KnowledgeBase::published()->orderBy('sort_order')->get()->groupBy('category');
        return view('support.knowledge-base', compact('articles'));
    }

    public function article(KnowledgeBase $article)
    {
        $article->incrementViews();
        return view('support.article', compact('article'));
    }
}
