@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bag me-2"></i>تفاصيل الطلب</h2>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>العودة للطلبات
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">المنتجات</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3">
                            @if($item->product && $item->product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image) }}" 
                                     class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->product_name }}</h6>
                                @if($item->product_sku)
                                    <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                @endif
                                <div class="text-muted">{{ $item->quantity }} × {{ number_format($item->price, 2) }} ر.س</div>
                            </div>
                            <div class="fw-bold">{{ number_format($item->total, 2) }} ر.س</div>
                        </div>
                        @if(!$loop->last)<hr>@endif
                    @endforeach
                </div>
            </div>
            
            <!-- Shipping Info -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-truck me-2"></i>معلومات الشحن</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>المستلم:</strong> {{ $order->shipping_name }}</p>
                            <p><strong>الهاتف:</strong> {{ $order->shipping_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>العنوان:</strong> {{ $order->shipping_address }}</p>
                            <p><strong>المدينة:</strong> {{ $order->shipping_city }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">ملخص الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>رقم الطلب:</span>
                        <span class="fw-bold">{{ $order->order_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>التاريخ:</span>
                        <span>{{ $order->created_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الحالة:</span>
                        <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>طريقة الدفع:</span>
                        <span>{{ $order->payment_method == 'cash_on_delivery' ? 'عند الاستلام' : 'تحويل بنكي' }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>المجموع الفرعي:</span>
                        <span>{{ number_format($order->subtotal, 2) }} ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الشحن:</span>
                        <span>{{ number_format($order->shipping_cost, 2) }} ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الضريبة:</span>
                        <span>{{ number_format($order->tax, 2) }} ر.س</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5 fw-bold">الإجمالي:</span>
                        <span class="fs-5 fw-bold text-primary">{{ number_format($order->total, 2) }} ر.س</span>
                    </div>
                </div>
            </div>
            
            <!-- Store Info -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i>المتجر</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @if($order->store->logo)
                            <img src="{{ asset('storage/' . $order->store->logo) }}" class="rounded-circle me-3" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $order->store->name }}</h6>
                            <small class="text-muted">{{ $order->store->city }}</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            @if($order->canBeCancelled())
                <form action="{{ route('orders.cancel', $order) }}" method="POST" 
                      onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-x-circle me-2"></i>إلغاء الطلب
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
