@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="d-flex align-items-center">
                <div class="icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="text-muted small">المستخدمين</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['users_count']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="d-flex align-items-center">
                <div class="icon bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <div class="text-muted small">المتاجر</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['stores_count']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="d-flex align-items-center">
                <div class="icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted small">المنتجات</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['products_count']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="d-flex align-items-center">
                <div class="icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="bi bi-bag"></i>
                </div>
                <div>
                    <div class="text-muted small">الطلبات</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats['orders_count']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="display-6 text-primary fw-bold">{{ number_format($stats['this_month_sales'], 0) }}</div>
                <div class="text-muted">ر.س مبيعات هذا الشهر</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="display-6 text-warning fw-bold">{{ $stats['pending_stores'] }}</div>
                <div class="text-muted">متجر بانتظار الموافقة</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="display-6 text-info fw-bold">{{ $stats['pending_orders'] }}</div>
                <div class="text-muted">طلب معلق</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">أحدث الطلبات</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المتجر</th>
                                <th>المجموع</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="fw-bold">{{ $order->order_number }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->store->name }}</td>
                                    <td>{{ number_format($order->total, 2) }} ر.س</td>
                                    <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                    <td>{{ $order->created_at->format('Y/m/d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">لا توجد طلبات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Stores -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-warning me-2"></i>بانتظار الموافقة</h5>
                <a href="{{ route('admin.stores.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @forelse($pendingStores as $store)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            @if($store->logo)
                                <img src="{{ asset('storage/' . $store->logo) }}" class="rounded-circle me-2" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    {{ mb_substr($store->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold">{{ $store->name }}</div>
                                <small class="text-muted">{{ $store->user->name }}</small>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <form action="{{ route('admin.stores.approve', $store) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check"></i></button>
                            </form>
                            <form action="{{ route('admin.stores.reject', $store) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mb-0">لا توجد طلبات معلقة</p>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Stores -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">أحدث المتاجر</h5>
            </div>
            <div class="card-body">
                @forelse($recentStores as $store)
                    <div class="d-flex align-items-center mb-3">
                        @if($store->logo)
                            <img src="{{ asset('storage/' . $store->logo) }}" class="rounded-circle me-2" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                {{ mb_substr($store->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold">{{ $store->name }}</div>
                            <small class="text-muted">{{ $store->city }} • {{ $store->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mb-0">لا توجد متاجر</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
