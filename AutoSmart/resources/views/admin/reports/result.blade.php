@extends('layouts.admin')
@section('title', 'نتائج التقرير')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ match($type) { 'sales' => 'تقرير المبيعات', 'products' => 'تقرير المنتجات', 'customers' => 'تقرير العملاء', 'stores' => 'تقرير المتاجر', 'support' => 'تقرير الدعم', 'auctions' => 'تقرير المزادات', 'gift_cards' => 'تقرير بطاقات الهدايا', default => 'التقرير' } }}</h1>
        <small class="text-muted">{{ $dateFrom }} - {{ $dateTo }}</small>
    </div>
    <div>
        <a href="{{ route('admin.reports.advanced') }}" class="btn btn-outline-secondary">تقرير جديد</a>
        <button class="btn btn-success"><i class="bi bi-download me-1"></i>تصدير</button>
    </div>
</div>

@if($type === 'sales')
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $data['total_orders'] }}</h3><small>إجمالي الطلبات</small></div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ number_format($data['total_revenue'], 2) }}</h3><small>إجمالي المبيعات</small></div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3 class="mb-0">{{ number_format($data['average_order'], 2) }}</h3><small>متوسط الطلب</small></div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $data['cancelled_orders'] }}</h3><small>طلبات ملغية</small></div></div></div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">المبيعات اليومية</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>التاريخ</th><th>عدد الطلبات</th><th>المبيعات</th></tr></thead>
                        <tbody>
                            @foreach($data['daily_sales'] as $day)
                                <tr><td>{{ $day->date }}</td><td>{{ $day->count }}</td><td>{{ number_format($day->total, 2) }} ر.س</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">أكثر المنتجات مبيعاً</h5></div>
                <div class="list-group list-group-flush">
                    @foreach($data['top_products'] as $product)
                        <div class="list-group-item d-flex justify-content-between"><span>{{ Str::limit($product->name, 25) }}</span><span class="badge bg-primary">{{ $product->sold ?? 0 }}</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@elseif($type === 'products')
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-primary mb-0">{{ $data['total_products'] }}</h3><small class="text-muted">إجمالي المنتجات</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-success mb-0">{{ $data['active_products'] }}</h3><small class="text-muted">منتجات نشطة</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-danger mb-0">{{ $data['out_of_stock'] }}</h3><small class="text-muted">نفذت من المخزون</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-warning mb-0">{{ $data['low_stock']->count() }}</h3><small class="text-muted">مخزون منخفض</small></div></div></div>
    </div>
@elseif($type === 'customers')
    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h3 class="text-primary mb-0">{{ $data['total_customers'] }}</h3><small class="text-muted">إجمالي العملاء</small></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h3 class="text-success mb-0">{{ $data['new_customers'] }}</h3><small class="text-muted">عملاء جدد</small></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h3 class="text-info mb-0">{{ $data['active_customers'] }}</h3><small class="text-muted">عملاء نشطين</small></div></div></div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">أفضل العملاء</h5></div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>العميل</th><th>البريد</th><th>إجمالي المشتريات</th></tr></thead>
                <tbody>
                    @foreach($data['top_customers'] as $customer)
                        <tr><td>{{ $customer->name }}</td><td>{{ $customer->email }}</td><td>{{ number_format($customer->orders_sum_total ?? 0, 2) }} ر.س</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <pre>{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
@endif
@endsection
