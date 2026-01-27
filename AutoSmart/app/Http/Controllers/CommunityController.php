<?php

namespace App\Http\Controllers;

use App\Models\CommunityGroup;
use App\Models\GroupPost;
use App\Models\PostComment;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index()
    {
        $groups = CommunityGroup::active()
            ->public()
            ->withCount('members')
            ->orderByDesc('members_count')
            ->paginate(12);
        
        return view('community.index', compact('groups'));
    }

    public function show(CommunityGroup $group)
    {
        $group->load(['carBrand', 'creator']);
        $posts = $group->posts()
            ->with(['user', 'comments.user'])
            ->latest()
            ->paginate(20);
        
        $isMember = auth()->check() && $group->isMember(auth()->user());
        
        return view('community.show', compact('group', 'posts', 'isMember'));
    }

    public function join(CommunityGroup $group)
    {
        if ($group->isMember(auth()->user())) {
            return back()->with('error', 'أنت عضو بالفعل');
        }
        
        $group->addMember(auth()->user());
        return back()->with('success', 'تم الانضمام للمجموعة');
    }

    public function leave(CommunityGroup $group)
    {
        $group->members()->detach(auth()->id());
        $group->decrement('members_count');
        return back()->with('success', 'تم مغادرة المجموعة');
    }

    public function createPost(Request $request, CommunityGroup $group)
    {
        if (!$group->isMember(auth()->user())) abort(403);
        
        $request->validate([
            'content' => 'required|string|max:5000',
            'images.*' => 'nullable|image|max:2048'
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('group-posts', 'public');
            }
        }

        $group->posts()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'images' => $images ?: null
        ]);

        return back()->with('success', 'تم نشر المنشور');
    }

    public function comment(Request $request, GroupPost $post)
    {
        $request->validate(['content' => 'required|string|max:1000']);
        
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);
        
        $post->increment('comments_count');
        return back();
    }

    public function create()
    {
        $carBrands = \App\Models\CarBrand::active()->get();
        return view('community.create', compact('carBrands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'car_brand_id' => 'nullable|exists:car_brands,id',
            'type' => 'required|in:public,private',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['created_by'] = auth()->id();
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('groups', 'public');
        }

        $group = CommunityGroup::create($validated);
        $group->addMember(auth()->user(), 'admin');

        return redirect()->route('community.show', $group)
            ->with('success', 'تم إنشاء المجموعة');
    }
}
