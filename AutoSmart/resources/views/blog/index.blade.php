@extends('layouts.app')

@section('title', 'المدونة')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h1>مدونة AutoSmart</h1>
        <p class="text-muted">نصائح، أدلة، وأخبار عالم السيارات وقطع الغيار</p>
    </div>
    
    <!-- Featured Posts -->
    @if($featuredPosts->count() > 0)
    <div class="mb-5">
        <div class="row">
            <div class="col-lg-8 mb-4 mb-lg-0">
                @php $mainPost = $featuredPosts->first() @endphp
                <a href="{{ route('blog.show', $mainPost->slug) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100">
                        @if($mainPost->featured_image)
                            <img src="{{ Storage::url($mainPost->featured_image) }}" class="card-img-top" style="height: 400px; object-fit: cover;">
                        @else
                            <div class="bg-primary" style="height: 400px;"></div>
                        @endif
                        <div class="card-img-overlay d-flex flex-column justify-content-end" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                            <span class="badge bg-primary mb-2" style="width: fit-content;">{{ $mainPost->category?->name ?? 'عام' }}</span>
                            <h2 class="text-white">{{ $mainPost->title }}</h2>
                            <p class="text-white-50 mb-0">{{ Str::limit($mainPost->excerpt, 150) }}</p>
                            <div class="text-white-50 mt-2">
                                <small>{{ $mainPost->author->name }} • {{ $mainPost->published_at->format('Y/m/d') }} • {{ $mainPost->reading_time }} دقائق قراءة</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4">
                @foreach($featuredPosts->skip(1)->take(2) as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="row g-0">
                                <div class="col-4">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" class="img-fluid rounded-start h-100" style="object-fit: cover;">
                                    @else
                                        <div class="bg-secondary h-100 rounded-start"></div>
                                    @endif
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark">{{ Str::limit($post->title, 50) }}</h6>
                                        <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        <!-- Posts Grid -->
        <div class="col-lg-8">
            <h4 class="mb-4">أحدث المقالات</h4>
            <div class="row">
                @foreach($posts as $post)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            @if($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-image text-muted display-4"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                @if($post->category)
                                    <a href="{{ route('blog.category', $post->category->slug) }}" class="badge bg-primary text-decoration-none mb-2">
                                        {{ $post->category->name }}
                                    </a>
                                @endif
                                <h5 class="card-title">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                                        {{ $post->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">{{ Str::limit($post->excerpt, 100) }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $post->published_at->format('Y/m/d') }}</small>
                                    <small class="text-muted">
                                        <i class="bi bi-eye me-1"></i>{{ number_format($post->views_count) }}
                                        <i class="bi bi-chat ms-2 me-1"></i>{{ $post->comments_count }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{ $posts->links() }}
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Search -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('blog.search') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="ابحث في المدونة...">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Categories -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">التصنيفات</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($categories->take(10) as $category)
                        <a href="{{ route('blog.category', $category->slug) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ $category->name }}
                            <span class="badge bg-primary rounded-pill">{{ $category->blog_posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body text-center py-4">
                    <i class="bi bi-envelope display-4 mb-3"></i>
                    <h5>اشترك في نشرتنا البريدية</h5>
                    <p class="small">احصل على أحدث المقالات والنصائح مباشرة</p>
                    <form>
                        <input type="email" class="form-control mb-2" placeholder="بريدك الإلكتروني">
                        <button type="submit" class="btn btn-light w-100">اشتراك</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
