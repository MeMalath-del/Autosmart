<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['storeComment']);
    }

    public function index()
    {
        $posts = BlogPost::published()
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->paginate(12);
        
        $featuredPosts = BlogPost::published()
            ->featured()
            ->with(['author'])
            ->limit(5)
            ->get();
        
        // Get categories that have blog posts
        $categories = Category::all();
        
        return view('blog.index', compact('posts', 'featuredPosts', 'categories'));
    }

    public function show(BlogPost $post)
    {
        if ($post->status !== 'published' && (!auth()->check() || auth()->id() !== $post->author_id)) {
            abort(404);
        }
        
        $post->incrementViews();
        $post->load(['author', 'category']);
        
        $comments = $post->comments()
            ->approved()
            ->roots()
            ->with(['user', 'replies.user'])
            ->orderByDesc('created_at')
            ->get();
        
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where(function($q) use ($post) {
                $q->where('category_id', $post->category_id)
                  ->orWhereJsonContains('tags', $post->tags);
            })
            ->limit(4)
            ->get();
        
        return view('blog.show', compact('post', 'comments', 'relatedPosts'));
    }

    public function category(Category $category)
    {
        $posts = BlogPost::published()
            ->where('category_id', $category->id)
            ->with(['author'])
            ->orderByDesc('published_at')
            ->paginate(12);
        
        return view('blog.category', compact('category', 'posts'));
    }

    public function tag(string $tag)
    {
        $posts = BlogPost::published()
            ->whereJsonContains('tags', $tag)
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->paginate(12);
        
        return view('blog.tag', compact('tag', 'posts'));
    }

    public function storeComment(Request $request, BlogPost $post)
    {
        $request->validate([
            'content' => 'required|string|min:5|max:1000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);
        
        $post->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => true, // Auto-approve for now
        ]);
        
        $post->increment('comments_count');
        
        return back()->with('success', 'تمت إضافة التعليق بنجاح');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:3',
        ]);
        
        $query = $request->q;
        
        $posts = BlogPost::published()
            ->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->orWhere('excerpt', 'like', "%{$query}%")
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->paginate(20);
        
        return view('blog.search', compact('posts', 'query'));
    }
}
