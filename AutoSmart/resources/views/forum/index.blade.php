@extends('layouts.app')

@section('title', 'منتدى السيارات')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">منتدى السيارات</h1>
            <p class="text-muted">شارك خبراتك واستفساراتك مع مجتمع AutoSmart</p>
        </div>
        <form action="{{ route('forum.search') }}" method="GET" class="d-flex" style="max-width: 300px;">
            <input type="text" name="q" class="form-control me-2" placeholder="ابحث في المنتدى...">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    
    <div class="row">
        <!-- Categories -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-grid me-2"></i>الأقسام</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($categories as $category)
                        <a href="{{ route('forum.category', $category->slug) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="bg-primary text-white rounded p-2 me-3">
                                        <i class="bi {{ $category->icon ?? 'bi-folder' }}"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">{{ $category->localized_name }}</h6>
                                        <small class="text-muted">{{ $category->description }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">{{ $category->topics_count }} موضوع</span>
                                    @if($category->recent_topics_count > 0)
                                        <br><small class="text-success">+{{ $category->recent_topics_count }} هذا الأسبوع</small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Topics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock me-2"></i>أحدث المواضيع</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($recentTopics as $topic)
                        <a href="{{ route('forum.topic', $topic->slug) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($topic->user->name) }}&size=40&background=random" 
                                     class="rounded-circle me-2" width="40" height="40">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 text-truncate">{{ $topic->title }}</h6>
                                    <small class="text-muted">{{ $topic->user->name }} • {{ $topic->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Popular Topics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-fire me-2"></i>الأكثر مشاهدة</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($popularTopics as $topic)
                        <a href="{{ route('forum.topic', $topic->slug) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-truncate">{{ $topic->title }}</span>
                                <span class="badge bg-secondary">{{ number_format($topic->views_count) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
