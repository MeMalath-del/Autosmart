@extends('layouts.app')
@section('title', 'تفاصيل طلب الصيانة')
@section('content')
<div class="container py-4">
    <a href="{{ route('maintenance.requests') }}" class="text-decoration-none"><i class="bi bi-arrow-right me-1"></i>العودة للطلبات</a>
    <h1 class="h3 mt-2 mb-4">طلب صيانة</h1>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تفاصيل الطلب</h5>
                    <span class="badge bg-{{ $maintenanceRequest->status === 'completed' ? 'success' : 'primary' }}">{{ $maintenanceRequest->status_label }}</span>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $maintenanceRequest->issue_description }}</p>
                    
                    @if($maintenanceRequest->userCar)
                        <p class="mb-2"><strong>السيارة:</strong> {{ $maintenanceRequest->userCar->display_name }}</p>
                    @elseif($maintenanceRequest->car_info)
                        <p class="mb-2"><strong>السيارة:</strong> {{ $maintenanceRequest->car_info }}</p>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-4"><p><strong>الأهمية:</strong> <span class="badge bg-{{ $maintenanceRequest->urgency === 'high' ? 'danger' : ($maintenanceRequest->urgency === 'medium' ? 'warning' : 'secondary') }}">{{ $maintenanceRequest->urgency_label }}</span></p></div>
                        <div class="col-md-4"><p><strong>المدينة:</strong> {{ $maintenanceRequest->city }}</p></div>
                        <div class="col-md-4"><p><strong>التاريخ:</strong> {{ $maintenanceRequest->created_at->format('Y/m/d H:i') }}</p></div>
                    </div>
                    
                    @if($maintenanceRequest->images)
                        <h6 class="mt-3">الصور:</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($maintenanceRequest->images as $img)
                                <img src="{{ asset('storage/'.$img) }}" class="rounded" style="width:100px;height:100px;object-fit:cover;">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">عروض الأسعار ({{ $maintenanceRequest->quotes->count() }})</h5></div>
                <div class="card-body p-0">
                    @forelse($maintenanceRequest->quotes as $quote)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $quote->workshop->name }}@if($quote->workshop->is_verified)<i class="bi bi-patch-check-fill text-primary ms-1"></i>@endif</h6>
                                    <small class="text-muted">{{ $quote->workshop->city }} - {{ $quote->workshop->rating }} <i class="bi bi-star-fill text-warning small"></i></small>
                                </div>
                                <span class="badge bg-{{ $quote->status === 'accepted' ? 'success' : ($quote->status === 'rejected' ? 'danger' : 'warning') }}">{{ $quote->status === 'accepted' ? 'مقبول' : ($quote->status === 'rejected' ? 'مرفوض' : 'معلق') }}</span>
                            </div>
                            <p class="mb-2">{{ $quote->description }}</p>
                            <div class="d-flex gap-4 mb-2">
                                <span><strong>العمالة:</strong> {{ number_format($quote->labor_cost, 2) }} ر.س</span>
                                <span><strong>القطع:</strong> {{ number_format($quote->parts_cost, 2) }} ر.س</span>
                                <span class="text-primary fw-bold"><strong>الإجمالي:</strong> {{ number_format($quote->total, 2) }} ر.س</span>
                            </div>
                            @if($quote->available_date)<small class="text-muted">متوفر: {{ $quote->available_date->format('Y/m/d') }}</small>@endif
                            
                            @if($quote->status === 'pending' && $maintenanceRequest->status !== 'booked')
                                <form action="{{ route('maintenance.quotes.accept', $quote) }}" method="POST" class="mt-2">@csrf
                                    <button class="btn btn-sm btn-success">قبول العرض</button>
                                </form>
                            @endif
                        </div>
                    @empty<div class="text-center py-4 text-muted">لم تصل عروض أسعار بعد</div>@endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="mb-3">ملخص</h6>
                    <p class="mb-2"><strong>الحالة:</strong> {{ $maintenanceRequest->status_label }}</p>
                    <p class="mb-2"><strong>عدد العروض:</strong> {{ $maintenanceRequest->quotes->count() }}</p>
                    @if($maintenanceRequest->preferred_date)<p class="mb-2"><strong>التاريخ المفضل:</strong> {{ $maintenanceRequest->preferred_date->format('Y/m/d') }}</p>@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
