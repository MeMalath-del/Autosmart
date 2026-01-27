@extends('layouts.app')

@section('title', 'تفاصيل طلب الضمان')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="mb-4">
                <a href="{{ route('warranty.index') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-right me-1"></i> العودة لطلبات الضمان
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $warrantyClaim->claim_number }}</h5>
                        <small class="text-muted">{{ $warrantyClaim->created_at->format('Y/m/d H:i') }}</small>
                    </div>
                    <span class="badge bg-{{ $warrantyClaim->status_color }} fs-6">
                        {{ $warrantyClaim->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 mb-4 p-3 bg-light rounded">
                        <img src="{{ $warrantyClaim->product->main_image }}" 
                             alt="{{ $warrantyClaim->product->name }}"
                             class="rounded"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h6 class="mb-1">{{ $warrantyClaim->product->name }}</h6>
                            <p class="text-muted small mb-1">
                                <a href="{{ route('stores.show', $warrantyClaim->store) }}">
                                    {{ $warrantyClaim->store->name }}
                                </a>
                            </p>
                            <p class="text-muted small mb-0">
                                رقم الطلب: <a href="{{ route('orders.show', $warrantyClaim->order) }}">{{ $warrantyClaim->order->order_number }}</a>
                            </p>
                        </div>
                    </div>

                    <h6>وصف المشكلة:</h6>
                    <p class="bg-light p-3 rounded">{{ $warrantyClaim->issue_description }}</p>

                    @if($warrantyClaim->images)
                        <h6>الصور المرفقة:</h6>
                        <div class="row g-2 mb-4">
                            @foreach($warrantyClaim->images as $image)
                                <div class="col-4 col-md-3">
                                    <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $image) }}" 
                                             alt="صورة"
                                             class="img-fluid rounded">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($warrantyClaim->store_response)
                        <div class="alert alert-info">
                            <h6><i class="bi bi-shop me-1"></i> رد المتجر:</h6>
                            <p class="mb-0">{{ $warrantyClaim->store_response }}</p>
                            @if($warrantyClaim->resolution)
                                <hr>
                                <p class="mb-0">
                                    <strong>القرار:</strong> {{ $warrantyClaim->resolution_label }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($warrantyClaim->admin_notes)
                        <div class="alert alert-secondary">
                            <h6><i class="bi bi-info-circle me-1"></i> ملاحظات الإدارة:</h6>
                            <p class="mb-0">{{ $warrantyClaim->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
