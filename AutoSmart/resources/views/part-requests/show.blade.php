@extends('layouts.app')

@section('title', 'تفاصيل طلب القطعة')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('part-requests.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-right me-1"></i> العودة للطلبات
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $partRequest->part_name }}</h5>
                    <span class="badge bg-{{ $partRequest->status === 'open' ? 'success' : ($partRequest->status === 'quoted' ? 'info' : 'secondary') }} fs-6">
                        {{ $partRequest->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    @if($partRequest->carBrand)
                        <div class="mb-3">
                            <strong>السيارة:</strong>
                            {{ $partRequest->carBrand->name }}
                            @if($partRequest->carModel) - {{ $partRequest->carModel->name }} @endif
                            @if($partRequest->car_year) ({{ $partRequest->car_year }}) @endif
                        </div>
                    @endif

                    @if($partRequest->part_number)
                        <div class="mb-3">
                            <strong>رقم القطعة:</strong> {{ $partRequest->part_number }}
                        </div>
                    @endif

                    @if($partRequest->description)
                        <div class="mb-3">
                            <strong>الوصف:</strong>
                            <p class="mb-0">{{ $partRequest->description }}</p>
                        </div>
                    @endif

                    @if($partRequest->budget_min || $partRequest->budget_max)
                        <div class="mb-3">
                            <strong>الميزانية:</strong>
                            @if($partRequest->budget_min && $partRequest->budget_max)
                                {{ number_format($partRequest->budget_min) }} - {{ number_format($partRequest->budget_max) }} ر.س
                            @elseif($partRequest->budget_max)
                                حتى {{ number_format($partRequest->budget_max) }} ر.س
                            @else
                                من {{ number_format($partRequest->budget_min) }} ر.س
                            @endif
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center text-muted small">
                        <span>
                            <i class="bi bi-clock"></i> {{ $partRequest->created_at->diffForHumans() }}
                        </span>
                        <span class="badge bg-{{ $partRequest->urgency === 'high' ? 'danger' : ($partRequest->urgency === 'medium' ? 'warning' : 'secondary') }}">
                            {{ $partRequest->urgency_label }}
                        </span>
                    </div>
                </div>
                @if($partRequest->status !== 'closed')
                    <div class="card-footer bg-white">
                        <form action="{{ route('part-requests.close', $partRequest) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                    onclick="return confirm('هل تريد إغلاق هذا الطلب؟')">
                                <i class="bi bi-x-lg me-1"></i> إغلاق الطلب
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">العروض المقدمة ({{ $partRequest->quotes->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($partRequest->quotes->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-clock display-4 text-muted"></i>
                            <p class="text-muted mt-2">لا توجد عروض بعد، سيتواصل معك البائعون قريباً</p>
                        </div>
                    @else
                        @foreach($partRequest->quotes as $quote)
                            <div class="border rounded p-3 mb-3 {{ $quote->status === 'accepted' ? 'border-success bg-success bg-opacity-10' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex gap-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">
                                                <a href="{{ route('stores.show', $quote->store) }}">
                                                    {{ $quote->store->name }}
                                                </a>
                                            </h6>
                                            @if($quote->store->is_verified)
                                                <span class="badge bg-success"><i class="bi bi-patch-check"></i> موثق</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="text-primary mb-0">{{ number_format($quote->price, 2) }} ر.س</h5>
                                        <span class="badge bg-{{ $quote->condition === 'new' ? 'success' : 'warning' }}">
                                            {{ $quote->condition_label }}
                                        </span>
                                    </div>
                                </div>

                                @if($quote->warranty)
                                    <p class="mb-2"><strong>الضمان:</strong> {{ $quote->warranty }}</p>
                                @endif
                                @if($quote->delivery_days)
                                    <p class="mb-2"><strong>مدة التوصيل:</strong> {{ $quote->delivery_days }} يوم</p>
                                @endif
                                @if($quote->notes)
                                    <p class="mb-2"><strong>ملاحظات:</strong> {{ $quote->notes }}</p>
                                @endif

                                @if($quote->status === 'pending' && $partRequest->status !== 'closed')
                                    <div class="d-flex gap-2 mt-3">
                                        <form action="{{ route('quotes.accept', $quote) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-lg me-1"></i> قبول العرض
                                            </button>
                                        </form>
                                        <form action="{{ route('quotes.reject', $quote) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-x-lg me-1"></i> رفض
                                            </button>
                                        </form>
                                        <a href="{{ route('conversations.start', $quote->store) }}?product_id={{ $quote->partRequest->id }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-chat me-1"></i> تواصل
                                        </a>
                                    </div>
                                @elseif($quote->status === 'accepted')
                                    <div class="alert alert-success mb-0 mt-3">
                                        <i class="bi bi-check-circle me-1"></i> تم قبول هذا العرض
                                    </div>
                                @elseif($quote->status === 'rejected')
                                    <div class="alert alert-secondary mb-0 mt-3">
                                        <i class="bi bi-x-circle me-1"></i> تم رفض هذا العرض
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($partRequest->images)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">الصور المرفقة</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($partRequest->images as $image)
                                <div class="col-6">
                                    <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $image) }}" 
                                             alt="صورة"
                                             class="img-fluid rounded">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
