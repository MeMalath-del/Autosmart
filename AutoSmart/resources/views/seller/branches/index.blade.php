@extends('layouts.seller')
@section('title', 'الفروع')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-geo-alt me-2"></i>فروع المتجر</h1>
    <a href="{{ route('seller.branches.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> فرع جديد</a>
</div>

@if($branches->isEmpty())
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-geo-alt display-1 text-muted"></i>
        <h4 class="mt-3">لا توجد فروع</h4>
        <p class="text-muted">أضف فروعاً لمتجرك لإدارة المخزون ونقاط البيع</p>
        <a href="{{ route('seller.branches.create') }}" class="btn btn-primary">إضافة فرع</a>
    </div></div>
@else
    <div class="row g-4">
        @foreach($branches as $branch)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">{{ $branch->name }}</h5>
                                <p class="text-muted small mb-0">{{ $branch->city }}</p>
                            </div>
                            <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }}">{{ $branch->is_active ? 'نشط' : 'معطل' }}</span>
                        </div>
                        <hr>
                        <p class="small"><i class="bi bi-geo-alt me-1"></i>{{ $branch->address }}</p>
                        @if($branch->phone)<p class="small"><i class="bi bi-telephone me-1"></i>{{ $branch->phone }}</p>@endif
                        @if($branch->is_pickup_point)<span class="badge bg-info"><i class="bi bi-box me-1"></i>نقطة استلام</span>@endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('seller.branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                        <a href="{{ route('seller.branches.inventory', $branch) }}" class="btn btn-sm btn-outline-secondary">المخزون</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
