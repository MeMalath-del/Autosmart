@extends('layouts.seller')

@section('title', 'الطلبات')

@section('content')
<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col">
        <a href="{{ route('seller.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card {{ request('status') == 'pending' ? 'border-warning' : '' }}">
                <div class="card-body text-center">
                    <div class="fs-4 fw-bold text-warning">{{ $stats['pending'] }}</div>
                    <small class="text-muted">معلق</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('seller.orders.index', ['status' => 'confirmed']) }}" class="text-decoration-none">
            <div class="card {{ request('status') == 'confirmed' ? 'border-info' : '' }}">
                <div class="card-body text-center">
                    <div class="fs-4 fw-bold text-info">{{ $stats['confirmed'] }}</div>
                    <small class="text-muted">مؤكد</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('seller.orders.index', ['status' => 'processing']) }}" class="text-decoration-none">
            <div class="card {{ request('status') == 'processing' ? 'border-primary' : '' }}">
                <div class="card-body text-center">
                    <div class="fs-4 fw-bold text-primary">{{ $stats['processing'] }}</div>
                    <small class="text-muted">قيد المعالجة</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('seller.orders.index', ['status' => 'shipped']) }}" class="text-decoration-none">
            <div class="card {{ request('status') == 'shipped' ? 'border-secondary' : '' }}">
                <div class="card-body text-center">
                    <div class="fs-4 fw-bold text-secondary">{{ $stats['shipped'] }}</div>
                    <small class="text-muted">تم الشحن</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('seller.orders.index', ['status' => 'delivered']) }}" class="text-decoration-none">
            <div class="card {{ request('status') == 'delivered' ? 'border-success' : '' }}">
                <div class="card-body text-center">
                    <div class="fs-4 fw-bold text-success">{{ $stats['delivered'] }}</div>
                    <small class="text-muted">تم التوصيل</small>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="بحث برقم الطلب أو اسم العميل..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">بحث</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>المنتجات</th>
                        <th>المجموع</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('seller.orders.show', $order) }}" class="fw-bold text-decoration-none">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $order->user->name }}</div>
                                <small class="text-muted">{{ $order->shipping_phone }}</small>
                            </td>
                            <td>{{ $order->items->count() }} منتج</td>
                            <td>{{ number_format($order->total, 2) }} ر.س</td>
                            <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td>{{ $order->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                <a href="{{ route('seller.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">لا توجد طلبات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
