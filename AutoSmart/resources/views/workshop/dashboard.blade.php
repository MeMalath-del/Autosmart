@extends('layouts.app')
@section('title', 'لوحة تحكم الورشة')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-wrench me-2"></i>{{ $workshop->name }}</h1>
        <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-outline-primary">عرض الصفحة العامة</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['pending_quotes'] }}</h3><small>عروض معلقة</small></div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['active_bookings'] }}</h3><small>حجوزات نشطة</small></div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['completed_this_month'] }}</h3><small>مكتمل هذا الشهر</small></div></div></div>
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ number_format($stats['total_revenue']) }}</h3><small>إجمالي الإيرادات</small></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="mb-0">طلبات الصيانة المتاحة</h5><a href="{{ route('workshop.requests') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a></div>
                <div class="card-body p-0">
                    @forelse($openRequests as $req)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div><strong>{{ Str::limit($req->issue_description, 50) }}</strong><br><small class="text-muted">{{ $req->user->name }} - {{ $req->created_at->diffForHumans() }}</small></div>
                                <span class="badge bg-{{ $req->urgency === 'high' ? 'danger' : ($req->urgency === 'medium' ? 'warning' : 'secondary') }}">{{ $req->urgency_label }}</span>
                            </div>
                        </div>
                    @empty<div class="text-center py-4 text-muted">لا توجد طلبات جديدة</div>@endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="mb-0">الحجوزات الأخيرة</h5><a href="{{ route('workshop.bookings') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a></div>
                <div class="card-body p-0">
                    @forelse($recentBookings as $booking)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><strong>{{ $booking->user->name }}</strong><br><small class="text-muted">{{ $booking->booking_date->format('Y/m/d') }} - {{ $booking->booking_time }}</small></div>
                                <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'in_progress' ? 'primary' : 'warning') }}">{{ $booking->status_label }}</span>
                            </div>
                        </div>
                    @empty<div class="text-center py-4 text-muted">لا توجد حجوزات</div>@endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
