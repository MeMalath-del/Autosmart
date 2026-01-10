@extends('layouts.app')
@section('title', 'تتبع الطلب #' . $order->order_number)
@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4"><i class="bi bi-geo-alt me-2"></i>تتبع الطلب #{{ $order->order_number }}</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div id="map" style="height:400px;background:#eee;display:flex;align-items:center;justify-content:center;">
                        <div class="text-center text-muted">
                            <i class="bi bi-geo-alt display-4"></i>
                            <p>خريطة التتبع المباشر</p>
                            <small>يتم تحديث الموقع تلقائياً</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">سجل التتبع</h5></div>
                <div class="card-body p-0">
                    <div class="timeline p-3">
                        @foreach($tracking as $track)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $track->status }}</h6>
                                    <p class="text-muted small mb-0">{{ $track->created_at->format('H:i - Y/m/d') }}</p>
                                    @if($track->notes)<p class="small mt-1">{{ $track->notes }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($currentLocation?->driver)
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">المندوب</h5></div>
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                        <h5>{{ $currentLocation->driver->name }}</h5>
                        <p class="text-muted"><i class="bi bi-star-fill text-warning"></i> {{ number_format($currentLocation->driver->rating, 1) }}</p>
                        <a href="tel:{{ $currentLocation->driver->phone }}" class="btn btn-success w-100"><i class="bi bi-telephone me-2"></i>اتصال</a>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header"><h5 class="mb-0">تفاصيل الطلب</h5></div>
                <div class="card-body">
                    <p><strong>الحالة:</strong> <span class="badge bg-info">{{ $order->status }}</span></p>
                    <p><strong>المجموع:</strong> {{ number_format($order->total, 2) }} ر.س</p>
                    @if($currentLocation?->estimated_minutes)
                        <div class="alert alert-success">
                            <i class="bi bi-clock me-2"></i>الوصول المتوقع خلال {{ $currentLocation->estimated_minutes }} دقيقة
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh location
setInterval(async () => {
    const res = await fetch('{{ route("orders.location", $order) }}');
    const data = await res.json();
    console.log('Location updated:', data);
}, 30000);
</script>
@endsection
