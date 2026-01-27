@extends('layouts.app')
@section('title', 'لوحة الشركاء')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-share me-2"></i>برنامج الشركاء التسويقيين</h1>

    <div class="row g-4 mb-4">
        <div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['clicks'] }}</h4><small>النقرات</small></div></div></div>
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['orders'] }}</h4><small>الطلبات</small></div></div></div>
        <div class="col-md-2"><div class="card"><div class="card-body text-center"><h4 class="text-info mb-0">{{ number_format($stats['conversion_rate'], 1) }}%</h4><small class="text-muted">التحويل</small></div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h4 class="mb-0">{{ number_format($stats['pending_earnings'], 2) }}</h4><small>أرباح معلقة</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h4 class="text-success mb-0">{{ number_format($stats['total_earnings'], 2) }}</h4><small class="text-muted">إجمالي الأرباح</small></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">روابطي التسويقية</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newLinkModal"><i class="bi bi-plus-lg"></i></button>
                </div>
                <div class="card-body p-0">
                    @if($links->isEmpty())
                        <div class="text-center py-4 text-muted">لا توجد روابط</div>
                    @else
                        <table class="table mb-0">
                            <thead><tr><th>الرابط</th><th>النقرات</th><th>التحويلات</th><th></th></tr></thead>
                            <tbody>
                                @foreach($links as $link)
                                    <tr>
                                        <td>
                                            {{ $link->name ?? 'رابط' }}<br>
                                            <small class="text-muted"><code>{{ route('affiliate.track', $link->code) }}</code></small>
                                        </td>
                                        <td>{{ $link->clicks }}</td>
                                        <td>{{ $link->conversions }}</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ route('affiliate.track', $link->code) }}')"><i class="bi bi-clipboard"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">سجل المبيعات</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>الطلب</th><th>المبلغ</th><th>العمولة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td><code>{{ $sale->order->order_number }}</code></td>
                                    <td>{{ number_format($sale->order_total, 2) }} ر.س</td>
                                    <td class="text-success">{{ number_format($sale->commission, 2) }} ر.س</td>
                                    <td><span class="badge bg-{{ $sale->status === 'paid' ? 'success' : ($sale->status === 'approved' ? 'info' : 'warning') }}">{{ $sale->status }}</span></td>
                                    <td>{{ $sale->created_at->format('Y/m/d') }}</td>
                                </tr>
                            @empty<tr><td colspan="5" class="text-center py-4">لا توجد مبيعات</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">معلومات الحساب</h5></div>
                <div class="card-body">
                    <p><strong>الرمز:</strong> <code class="fs-5">{{ $affiliate->code }}</code></p>
                    <p><strong>نسبة العمولة:</strong> {{ $affiliate->commission_rate }}%</p>
                    <p><strong>الحد الأدنى للسحب:</strong> {{ number_format($affiliate->minimum_payout, 2) }} ر.س</p>
                    <hr>
                    @if($affiliate->canRequestPayout())
                        <form action="{{ route('affiliate.payout') }}" method="POST">@csrf
                            <button class="btn btn-success w-100"><i class="bi bi-cash me-2"></i>طلب سحب {{ number_format($affiliate->pending_earnings, 2) }} ر.س</button>
                        </form>
                    @else
                        <div class="alert alert-info small mb-0">يجب أن تصل أرباحك المعلقة إلى {{ number_format($affiliate->minimum_payout, 2) }} ر.س لطلب السحب</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إنشاء رابط جديد</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('affiliate.links.create') }}" method="POST">@csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">اسم الرابط (اختياري)</label><input type="text" name="name" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">الوجهة</label><input type="url" name="destination_url" class="form-control" value="{{ url('/') }}" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">إنشاء</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
