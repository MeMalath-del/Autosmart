@extends('layouts.app')

@section('title', 'طلباتي')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-bag me-2"></i>طلباتي</h2>
    
    @if($orders->isNotEmpty())
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الطلب</th>
                            <th>المتجر</th>
                            <th>المجموع</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="fw-bold text-decoration-none">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('stores.show', $order->store->slug) }}" class="text-decoration-none">
                                        {{ $order->store->name }}
                                    </a>
                                </td>
                                <td>{{ number_format($order->total, 2) }} ر.س</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                </td>
                                <td>{{ $order->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-bag-x display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات</h4>
            <p class="text-muted">لم تقم بأي طلبات بعد</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">تصفح المنتجات</a>
        </div>
    @endif
</div>
@endsection
