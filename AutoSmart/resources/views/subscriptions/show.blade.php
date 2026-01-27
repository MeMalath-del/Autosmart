@extends('layouts.app')
@section('title', 'إدارة الاشتراك')
@section('content')
<div class="container py-4">
    <a href="{{ route('subscriptions.index') }}" class="text-decoration-none mb-3 d-inline-block"><i class="bi bi-arrow-right me-1"></i>العودة للاشتراكات</a>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $subscription->plan->localized_name }}</h5>
                    <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : 'secondary' }} fs-6">{{ $subscription->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">تاريخ البدء</p>
                            <h6>{{ $subscription->starts_at->format('Y/m/d') }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">تاريخ الانتهاء</p>
                            <h6>{{ $subscription->ends_at?->format('Y/m/d') ?? 'غير محدد' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">التجديد التلقائي</p>
                            <h6>{{ $subscription->auto_renew ? 'نعم' : 'لا' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">الفاتورة القادمة</p>
                            <h6>{{ $subscription->next_billing_at?->format('Y/m/d') ?? '-' }}</h6>
                        </div>
                    </div>

                    @if($subscription->plan->features)
                        <hr>
                        <h6>المميزات المشمولة:</h6>
                        <ul class="mb-0">
                            @foreach($subscription->plan->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            @if($subscription->deliveries->count())
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">سجل التوصيلات</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>التاريخ</th><th>الحالة</th><th>رقم الطلب</th></tr></thead>
                            <tbody>
                                @foreach($subscription->deliveries as $delivery)
                                    <tr>
                                        <td>{{ $delivery->scheduled_date->format('Y/m/d') }}</td>
                                        <td><span class="badge bg-{{ $delivery->status === 'delivered' ? 'success' : 'info' }}">{{ $delivery->status }}</span></td>
                                        <td>{{ $delivery->order?->order_number ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">إدارة الاشتراك</h5></div>
                <div class="card-body">
                    @if($subscription->isActive())
                        @if($subscription->status === 'active')
                            <form action="{{ route('subscriptions.pause', $subscription) }}" method="POST" class="mb-2">@csrf
                                <button type="submit" class="btn btn-warning w-100"><i class="bi bi-pause-circle me-2"></i>إيقاف مؤقت</button>
                            </form>
                        @else
                            <form action="{{ route('subscriptions.resume', $subscription) }}" method="POST" class="mb-2">@csrf
                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-play-circle me-2"></i>استئناف</button>
                            </form>
                        @endif

                        <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الاشتراك؟')">@csrf
                            <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-2"></i>إلغاء الاشتراك</button>
                        </form>
                    @else
                        <div class="alert alert-info mb-0">هذا الاشتراك {{ $subscription->status }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
