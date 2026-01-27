@extends('layouts.app')
@section('title', 'لوحة المؤثرين')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-stars me-2"></i>لوحة المؤثرين</h1>
            <small class="text-muted">مرحباً {{ auth()->user()->name }}</small>
        </div>
        @if($influencer->is_verified)<span class="badge bg-primary"><i class="bi bi-patch-check me-1"></i>موثق</span>@endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white"><div class="card-body text-center">
                <h6 class="text-white-50">الرمز الترويجي</h6>
                <h4 class="mb-0 font-monospace">{{ $influencer->code }}</h4>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center">
                <h6 class="text-muted">إجمالي المبيعات</h6>
                <h3 class="text-primary mb-0">{{ $influencer->total_sales }}</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center">
                <h6 class="text-muted">إجمالي العمولات</h6>
                <h3 class="text-success mb-0">{{ number_format($influencer->total_earnings, 2) }} ر.س</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center">
                <h6 class="text-muted">نسبة العمولة</h6>
                <h3 class="mb-0">{{ $influencer->commission_rate }}%</h3>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">سجل المبيعات</h5></div>
                <div class="card-body p-0">
                    @if($sales->isEmpty())
                        <div class="text-center py-4 text-muted">لا توجد مبيعات بعد</div>
                    @else
                        <table class="table mb-0">
                            <thead><tr><th>رقم الطلب</th><th>قيمة الطلب</th><th>العمولة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr>
                                        <td><code>{{ $sale->order->order_number }}</code></td>
                                        <td>{{ number_format($sale->order_total, 2) }} ر.س</td>
                                        <td class="text-success">{{ number_format($sale->commission, 2) }} ر.س</td>
                                        <td><span class="badge bg-{{ $sale->status === 'paid' ? 'success' : 'warning' }}">{{ $sale->status }}</span></td>
                                        <td>{{ $sale->created_at->format('Y/m/d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">رابط الإحالة</h5></div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="refLink" value="{{ route('influencer.link', $influencer->code) }}" readonly>
                        <button class="btn btn-outline-primary" onclick="copyLink()"><i class="bi bi-clipboard"></i></button>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('influencer.link', $influencer->code)) }}" target="_blank" class="btn btn-outline-dark"><i class="bi bi-twitter me-2"></i>شارك على تويتر</a>
                        <a href="https://wa.me/?text={{ urlencode(route('influencer.link', $influencer->code)) }}" target="_blank" class="btn btn-success"><i class="bi bi-whatsapp me-2"></i>شارك على واتساب</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">حساباتي</h5></div>
                <div class="card-body">
                    @if($influencer->youtube)<p><i class="bi bi-youtube text-danger me-2"></i>{{ $influencer->youtube }}</p>@endif
                    @if($influencer->instagram)<p><i class="bi bi-instagram text-primary me-2"></i>{{ '@' . $influencer->instagram }}</p>@endif
                    @if($influencer->twitter)<p><i class="bi bi-twitter text-info me-2"></i>{{ '@' . $influencer->twitter }}</p>@endif
                    @if($influencer->tiktok)<p><i class="bi bi-tiktok me-2"></i>{{ '@' . $influencer->tiktok }}</p>@endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>function copyLink() { navigator.clipboard.writeText(document.getElementById('refLink').value); alert('تم النسخ!'); }</script>
@endsection
