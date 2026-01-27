@extends('layouts.app')
@section('title', $group->name)
@section('content')
<div class="container py-4">
    <div class="card mb-4">
        <div class="row g-0">
            @if($group->image)
                <div class="col-md-3"><img src="{{ asset('storage/' . $group->image) }}" class="img-fluid rounded-start h-100 object-fit-cover"></div>
            @endif
            <div class="col-md-{{ $group->image ? '9' : '12' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="card-title">{{ $group->name }}</h3>
                            @if($group->carBrand)<span class="badge bg-secondary">{{ $group->carBrand->name }}</span>@endif
                        </div>
                        @auth
                            @if($isMember)
                                <form action="{{ route('community.leave', $group) }}" method="POST">@csrf<button class="btn btn-outline-danger">مغادرة</button></form>
                            @else
                                <form action="{{ route('community.join', $group) }}" method="POST">@csrf<button class="btn btn-primary">انضمام</button></form>
                            @endif
                        @endauth
                    </div>
                    <p class="card-text mt-2">{{ $group->description }}</p>
                    <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $group->members_count }} عضو · <i class="bi bi-chat me-1"></i>{{ $group->posts_count }} منشور</small>
                </div>
            </div>
        </div>
    </div>

    @if($isMember)
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('community.post', $group) }}" method="POST" enctype="multipart/form-data">@csrf
                    <textarea name="content" class="form-control mb-2" rows="3" placeholder="شارك شيئاً مع المجموعة..." required></textarea>
                    <div class="d-flex justify-content-between">
                        <input type="file" name="images[]" class="form-control form-control-sm w-auto" multiple accept="image/*">
                        <button type="submit" class="btn btn-primary">نشر</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @forelse($posts as $post)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;">{{ substr($post->user->name, 0, 1) }}</div>
                    <div class="ms-2">
                        <strong>{{ $post->user->name }}</strong>
                        <div class="text-muted small">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <p>{{ $post->content }}</p>
                @if($post->images)
                    <div class="d-flex gap-2 mb-2">
                        @foreach($post->images as $image)<img src="{{ asset('storage/' . $image) }}" class="rounded" style="max-height:150px;">@endforeach
                    </div>
                @endif
                <div class="d-flex gap-3 text-muted">
                    <span><i class="bi bi-heart me-1"></i>{{ $post->likes_count }}</span>
                    <span><i class="bi bi-chat me-1"></i>{{ $post->comments_count }}</span>
                </div>

                @if($post->comments->count())
                    <hr>
                    @foreach($post->comments->take(3) as $comment)
                        <div class="d-flex mb-2">
                            <small><strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}</small>
                        </div>
                    @endforeach
                @endif

                @auth
                    <form action="{{ route('community.comment', $post) }}" method="POST" class="mt-2">@csrf
                        <div class="input-group input-group-sm"><input type="text" name="content" class="form-control" placeholder="أضف تعليقاً..."><button class="btn btn-primary">إرسال</button></div>
                    </form>
                @endauth
            </div>
        </div>
    @empty<div class="text-center py-4 text-muted">لا توجد منشورات</div>@endforelse

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
@endsection
