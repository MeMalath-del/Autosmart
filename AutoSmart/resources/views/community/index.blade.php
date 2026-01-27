@extends('layouts.app')
@section('title', 'المجتمع')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-people me-2"></i>مجتمع AutoSmart</h1>
        @auth<a href="{{ route('community.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> إنشاء مجموعة</a>@endauth
    </div>

    @if($groups->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-people display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد مجموعات</h4>
            <p class="text-muted">كن أول من ينشئ مجموعة!</p>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($groups as $group)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        @if($group->image)
                            <img src="{{ asset('storage/' . $group->image) }}" class="card-img-top" style="height:150px;object-fit:cover;">
                        @else
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height:150px;">
                                <i class="bi bi-people text-white display-3"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $group->name }}</h5>
                            @if($group->carBrand)<span class="badge bg-secondary mb-2">{{ $group->carBrand->name }}</span>@endif
                            <p class="text-muted small">{{ Str::limit($group->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $group->members_count }} عضو</small>
                                <a href="{{ route('community.show', $group) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $groups->links() }}</div>
    @endif
</div>
@endsection
