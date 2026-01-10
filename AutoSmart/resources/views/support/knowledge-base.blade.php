@extends('layouts.app')
@section('title', 'قاعدة المعرفة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-book me-2"></i>قاعدة المعرفة</h1>

    <div class="row justify-content-center mb-4">
        <div class="col-lg-6">
            <form class="input-group">
                <input type="text" class="form-control" placeholder="ابحث في المقالات...">
                <button class="btn btn-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    @foreach($articles as $category => $items)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-folder me-2"></i>{{ $category }}</h5>
            </div>
            <div class="list-group list-group-flush">
                @foreach($items as $article)
                    <a href="{{ route('support.article', $article) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $article->title }}</h6>
                            <small class="text-muted">{{ Str::limit(strip_tags($article->content), 100) }}</small>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-eye me-1"></i>{{ $article->views_count }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    @if($articles->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-book display-1"></i>
            <p class="mt-2">لا توجد مقالات بعد</p>
        </div>
    @endif
</div>
@endsection
