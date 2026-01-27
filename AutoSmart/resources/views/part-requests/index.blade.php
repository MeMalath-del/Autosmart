@extends('layouts.app')

@section('title', 'طلبات قطع الغيار')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">طلبات قطع الغيار</h1>
        <a href="{{ route('part-requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> طلب جديد
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-search display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات</h4>
            <p class="text-muted">أنشئ طلباً للبحث عن قطعة غيار غير متوفرة</p>
            <a href="{{ route('part-requests.create') }}" class="btn btn-primary">طلب قطعة غيار</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($requests as $request)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $request->urgency === 'high' ? 'danger' : ($request->urgency === 'medium' ? 'warning' : 'secondary') }}">
                                {{ $request->urgency_label }}
                            </span>
                            <span class="badge bg-{{ $request->status === 'open' ? 'success' : ($request->status === 'quoted' ? 'info' : 'secondary') }}">
                                {{ $request->status_label }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $request->part_name }}</h5>
                            @if($request->carBrand)
                                <p class="text-muted mb-2">
                                    <i class="bi bi-car-front"></i>
                                    {{ $request->carBrand->name }}
                                    @if($request->carModel) - {{ $request->carModel->name }} @endif
                                    @if($request->car_year) ({{ $request->car_year }}) @endif
                                </p>
                            @endif
                            @if($request->part_number)
                                <p class="text-muted small mb-2">
                                    رقم القطعة: {{ $request->part_number }}
                                </p>
                            @endif
                            <p class="text-muted small mb-0">
                                {{ Str::limit($request->description, 100) }}
                            </p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">
                                    <i class="bi bi-chat-quote"></i> {{ $request->quotes_count }} عرض
                                </span>
                            </div>
                            <a href="{{ route('part-requests.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
