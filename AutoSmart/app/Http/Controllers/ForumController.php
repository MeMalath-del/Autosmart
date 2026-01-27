<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'category', 'topic']);
    }

    public function index()
    {
        $categories = ForumCategory::withCount(['topics', 'topics as recent_topics_count' => function($q) {
            $q->where('created_at', '>=', now()->subDays(7));
        }])->orderBy('sort_order')->get();
        
        $recentTopics = ForumTopic::with(['user', 'category'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        $popularTopics = ForumTopic::with(['user', 'category'])
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();
        
        return view('forum.index', compact('categories', 'recentTopics', 'popularTopics'));
    }

    public function category(ForumCategory $category)
    {
        $pinnedTopics = $category->topics()->pinned()->with('user')->get();
        
        $topics = $category->topics()
            ->where('is_pinned', false)
            ->with('user')
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('forum.category', compact('category', 'pinnedTopics', 'topics'));
    }

    public function topic(ForumTopic $topic)
    {
        $topic->incrementViews();
        $topic->load(['user', 'category']);
        
        $replies = $topic->replies()
            ->whereNull('parent_id')
            ->with(['user', 'children.user'])
            ->orderBy('created_at')
            ->paginate(20);
        
        return view('forum.topic', compact('topic', 'replies'));
    }

    public function createTopic(ForumCategory $category)
    {
        return view('forum.create-topic', compact('category'));
    }

    public function storeTopic(Request $request, ForumCategory $category)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string|min:20',
            'tags' => 'nullable|string',
        ]);
        
        $topic = $category->topics()->create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'content' => $request->content,
            'tags' => $request->tags ? explode(',', $request->tags) : null,
        ]);
        
        $category->increment('topics_count');
        
        return redirect()->route('forum.topic', $topic)
            ->with('success', 'تم إنشاء الموضوع بنجاح');
    }

    public function storeReply(Request $request, ForumTopic $topic)
    {
        $request->validate([
            'content' => 'required|string|min:10',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);
        
        if ($topic->is_locked) {
            return back()->with('error', 'الموضوع مغلق');
        }
        
        $reply = $topic->replies()->create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);
        
        $topic->increment('replies_count');
        $topic->update(['last_reply_at' => now()]);
        $topic->category->increment('posts_count');
        
        return back()->with('success', 'تمت إضافة الرد بنجاح');
    }

    public function markSolution(ForumReply $reply)
    {
        $topic = $reply->topic;
        
        if ($topic->user_id !== auth()->id()) {
            abort(403);
        }
        
        $reply->markAsSolution();
        
        return back()->with('success', 'تم تحديد الرد كحل');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:3',
        ]);
        
        $query = $request->q;
        
        $topics = ForumTopic::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->with(['user', 'category'])
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('forum.search', compact('topics', 'query'));
    }
}
