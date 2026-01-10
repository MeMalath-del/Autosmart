@extends('layouts.seller')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">التقارير والإحصائيات</h1>
    <div class="d-flex gap-2">
        <form action="" method="GET" class="d-flex gap-2">
            <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>آخر أسبوع</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>آخر شهر</option>
                <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>آخر 3 أشهر</option>
                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>آخر سنة</option>
            </select>
        </form>
        <a href="{{ route('seller.reports.export', ['period' => $period]) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download me-1"></i> تصدير CSV
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="mb-1">إجمالي المبيعات</h6>
                <h3 class="mb-0">{{ number_format($stats['total_sales'], 2) }} ر.س</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="mb-1">عدد الطلبات</h6>
                <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="mb-1">متوسط قيمة الطلب</h6>
                <h3 class="mb-0">{{ number_format($stats['avg_order_value'], 2) }} ر.س</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="mb-1">المنتجات المباعة</h6>
                <h3 class="mb-0">{{ $stats['total_products_sold'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">المبيعات اليومية</h5>
            </div>
            <div class="card-body">
                @if($dailySales->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up display-4 text-muted"></i>
                        <p class="text-muted mt-2">لا توجد مبيعات في هذه الفترة</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>عدد الطلبات</th>
                                    <th>المبيعات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailySales as $day)
                                    <tr>
                                        <td>{{ $day->date }}</td>
                                        <td>{{ $day->orders }}</td>
                                        <td>{{ number_format($day->total, 2) }} ر.س</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">توزيع حالات الطلبات</h5>
            </div>
            <div class="card-body">
                @php
                    $statuses = [
                        'pending' => ['label' => 'قيد الانتظار', 'color' => 'warning'],
                        'confirmed' => ['label' => 'مؤكد', 'color' => 'info'],
                        'processing' => ['label' => 'قيد التجهيز', 'color' => 'primary'],
                        'shipped' => ['label' => 'تم الشحن', 'color' => 'info'],
                        'delivered' => ['label' => 'تم التوصيل', 'color' => 'success'],
                        'cancelled' => ['label' => 'ملغي', 'color' => 'danger'],
                    ];
                @endphp
                @foreach($statuses as $key => $status)
                    @if(isset($ordersByStatus[$key]) && $ordersByStatus[$key] > 0)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-{{ $status['color'] }}">{{ $status['label'] }}</span>
                            <span class="fw-bold">{{ $ordersByStatus[$key] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">المنتجات الأكثر مبيعاً</h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">لا توجد مبيعات بعد</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($topProducts as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="{{ $product->main_image }}" 
                                         alt="{{ $product->name }}"
                                         class="rounded"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="text-truncate" style="max-width: 150px;">{{ $product->name }}</span>
                                </div>
                                <span class="badge bg-primary">{{ $product->sales_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
