@extends('layouts.app')
@section('title', 'الاستشارات الفنية')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-headset me-2"></i>الاستشارات الفنية</h1>
    <p class="text-center text-muted mb-5">احصل على استشارة من خبراء السيارات</p>

    <div class="row g-4 mb-5">
        @forelse($experts as $expert)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                        <h5>{{ $expert->user->name }}</h5>
                        <p class="text-primary mb-1">{{ $expert->specialty }}</p>
                        <p class="text-muted small"><i class="bi bi-star-fill text-warning"></i> {{ number_format($expert->rating, 1) }} · {{ $expert->experience_years }} سنة خبرة</p>
                        <p class="fw-bold text-success">{{ number_format($expert->hourly_rate, 2) }} ر.س/ساعة</p>
                        <a href="{{ route('consultations.book', $expert) }}" class="btn btn-primary w-100">حجز جلسة</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info text-center">لا يوجد خبراء متاحين حالياً</div></div>
        @endforelse
    </div>

    @if($mySessions->count())
        <h4 class="mb-3">جلساتي</h4>
        <div class="card">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>رقم الجلسة</th><th>الخبير</th><th>الموضوع</th><th>الموعد</th><th>الحالة</th><th></th></tr></thead>
                    <tbody>
                        @foreach($mySessions as $session)
                            <tr>
                                <td><code>{{ $session->session_number }}</code></td>
                                <td>{{ $session->expert->name }}</td>
                                <td>{{ $session->topic }}</td>
                                <td>{{ $session->scheduled_at->format('Y/m/d H:i') }}</td>
                                <td><span class="badge bg-{{ $session->status === 'completed' ? 'success' : 'info' }}">{{ $session->status }}</span></td>
                                <td><a href="{{ route('consultations.show', $session) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
