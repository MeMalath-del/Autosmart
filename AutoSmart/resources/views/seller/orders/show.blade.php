@extends('layouts.seller')

@section('title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المنتجات</h5>
                <span class="badge bg-{{ $order->status_color }} fs-6">{{ $order->status_label }}</span>
            </div>
            <div class="card-body">
                @foreach($order->items as $item)
                    <div class="d-flex align-items-center mb-3">
                        @if($item->product && $item->product->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $item->product->images->first()->image) }}" 
                                 class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 70px; height: 70px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->product_name }}</h6>
                            @if($item->product_sku)
                                <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            <div>{{ $item->quantity }} × {{ number_format($item->price, 2) }}</div>
                            <div class="fw-bold">{{ number_format($item->total, 2) }} ر.س</div>
                        </div>
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
                        <p><strong>المدينة:</strong> {{ $order->shipping_city }}</p>
                        <p><strong>العنوان:</strong> {{ $order->shipping_address }}</p>
                    </div>
                </div>
                @if($order->notes)
                    <div class="alert alert-info mb-0">
                        <strong>ملاحظات العميل:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Admin Notes -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>ملاحظات داخلية</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.orders.notes', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <textarea name="admin_notes" class="form-control mb-2" rows="3" 
                              placeholder="ملاحظات للفريق...">{{ $order->admin_notes }}</textarea>
                    <button type="submit" class="btn btn-outline-primary btn-sm">حفظ الملاحظات</button>
                </form>
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
                    <span>طريقة الدفع:</span>
                    <span>{{ $order->payment_method == 'cash_on_delivery' ? 'عند الاستلام' : 'تحويل بنكي' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>حالة الدفع:</span>
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                        {{ $order->payment_status_label }}
                    </span>
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
        
        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>العميل</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <img src="{{ $order->user->avatar_url }}" class="rounded-circle me-3" 
                         style="width: 50px; height: 50px;">
                    <div>
                        <div class="fw-bold">{{ $order->user->name }}</div>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">الإجراءات</h5>
            </div>
            <div class="card-body">
                @if($order->status == 'pending')
                    <form action="{{ route('seller.orders.confirm', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-2"></i>تأكيد الطلب
                        </button>
                    </form>
                @endif
                
                @if($order->status == 'confirmed')
                    <form action="{{ route('seller.orders.process', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-gear me-2"></i>بدء المعالجة
                        </button>
                    </form>
                @endif
                
                @if(in_array($order->status, ['confirmed', 'processing']))
                    <form action="{{ route('seller.orders.ship', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-info w-100">
                            <i class="bi bi-truck me-2"></i>تم الشحن
                        </button>
                    </form>
                @endif
                
                @if($order->status == 'shipped')
                    <form action="{{ route('seller.orders.deliver', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i>تم التوصيل
                        </button>
                    </form>
                @endif
                
                @if($order->canBeCancelled())
                    <form action="{{ route('seller.orders.cancel', $order) }}" method="POST"
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
</div>
@endsection
