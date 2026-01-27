@extends('layouts.app')
@section('title', 'طلبات الصيانة')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-tools me-2"></i>طلبات الصيانة</h1>
        <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> طلب جديد</a>
    </div>

    @if($requests->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-tools display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات صيانة</h4>
            <p class="text-muted">أنشئ طلب صيانة للحصول على عروض من الورش</p>
            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">إنشاء طلب</a>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($requests as $request)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'cancelled' ? 'danger' : 'primary') }}">{{ $request->status_label }}</span>
                                <span class="badge bg-{{ $request->urgency === 'high' ? 'danger' : ($request->urgency === 'medium' ? 'warning' : 'secondary') }}">{{ $request->urgency_label }}</span>
                            </div>
                            <h5 class="card-title">{{ Str::limit($request->issue_description, 60) }}</h5>
                            @if($request->userCar)<p class="text-muted small mb-2"><i class="bi bi-car-front me-1"></i>{{ $request->userCar->display_name }}</p>@endif
                            <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $request->city }}</p>
                            <p class="text-muted small"><i class="bi bi-calendar me-1"></i>{{ $request->created_at->format('Y/m/d') }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">{{ $request->quotes_count }} عرض</span>
                                <a href="{{ route('maintenance.requests.show', $request) }}" class="btn btn-sm btn-outline-primary">عرض التفاصيل</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
