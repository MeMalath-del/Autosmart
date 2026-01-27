@extends('layouts.seller')

@section('title', 'لوحة التحكم')

@section('content')
<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center">
            <div class="icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="text-muted small">المنتجات</div>
                <div class="fs-4 fw-bold">{{ $stats['products_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center">
            <div class="icon bg-success bg-opacity-10 text-success me-3">
                <i class="bi bi-bag"></i>
            </div>
            <div>
                <div class="text-muted small">الطلبات</div>
                <div class="fs-4 fw-bold">{{ $stats['orders_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center">
            <div class="icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="text-muted small">طلبات معلقة</div>
                <div class="fs-4 fw-bold">{{ $stats['pending_orders'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center">
            <div class="icon bg-info bg-opacity-10 text-info me-3">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
                <div class="text-muted small">مبيعات الشهر</div>
                <div class="fs-4 fw-bold">{{ number_format($stats['this_month_sales'], 0) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">أحدث الطلبات</h5>
                <a href="{{ route('seller.orders.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المجموع</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('seller.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ number_format($order->total, 2) }} ر.س</td>
                                    <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                    <td>{{ $order->created_at->format('Y/m/d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد طلبات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Low Stock -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>مخزون منخفض</h5>
            </div>
            <div class="card-body">
                @forelse($lowStockProducts as $product)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-bold">{{ Str::limit($product->name, 25) }}</div>
                            <small class="text-danger">متبقي: {{ $product->quantity }}</small>
                        </div>
                        <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-center text-muted mb-0">لا توجد منتجات بمخزون منخفض</p>
                @endforelse
            </div>
        </div>
        
        <!-- Part Requests -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-search text-primary me-2"></i>طلبات قطع غيار</h5>
            </div>
            <div class="card-body">
                @forelse($partRequests as $request)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="fw-bold">{{ $request->part_name }}</div>
                        <small class="text-muted">
                            {{ $request->carBrand?->name }} {{ $request->carModel?->name }}
                            @if($request->car_year) ({{ $request->car_year }}) @endif
                        </small>
                        <div class="mt-1">
                            <span class="badge bg-{{ $request->urgency_color }}">{{ $request->urgency_label }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mb-0">لا توجد طلبات</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
