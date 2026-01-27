@extends('layouts.app')

@section('title', 'طلبات الضمان')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">طلبات الضمان</h1>

    @if($claims->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-shield-check display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات ضمان</h4>
            <p class="text-muted">يمكنك تقديم طلب ضمان من صفحة تفاصيل الطلب</p>
            <a href="{{ route('orders.index') }}" class="btn btn-primary">عرض الطلبات</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($claims as $claim)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $claim->claim_number }}</span>
                            <span class="badge bg-{{ $claim->status_color }}">{{ $claim->status_label }}</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3 mb-3">
                                <img src="{{ $claim->product->main_image }}" 
                                     alt="{{ $claim->product->name }}"
                                     class="rounded"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1">{{ $claim->product->name }}</h6>
                                    <small class="text-muted">{{ $claim->store->name }}</small>
                                </div>
                            </div>
                            <p class="text-muted small mb-3">{{ Str::limit($claim->issue_description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ $claim->created_at->format('Y/m/d') }}
                                </small>
                                <a href="{{ route('warranty.show', $claim) }}" class="btn btn-sm btn-outline-primary">
                                    التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $claims->links() }}
        </div>
    @endif
</div>
@endsection
