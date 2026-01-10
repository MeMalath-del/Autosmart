@extends('layouts.app')

@section('title', 'تقديم طلب ضمان')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تقديم طلب ضمان</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 mb-4 p-3 bg-light rounded">
                        <img src="{{ $product->main_image }}" 
                             alt="{{ $product->name }}"
                             class="rounded"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <p class="text-muted small mb-1">رقم الطلب: {{ $order->order_number }}</p>
                            <p class="text-muted small mb-0">
                                @if($product->warranty)
                                    <i class="bi bi-shield-check text-success"></i> ضمان: {{ $product->warranty }}
                                @else
                                    <i class="bi bi-shield text-muted"></i> لا يوجد ضمان محدد
                                @endif
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('warranty.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_item_id" value="{{ $orderItem->id }}">

                        <div class="mb-4">
                            <label class="form-label">وصف المشكلة</label>
                            <textarea name="issue_description" 
                                      class="form-control @error('issue_description') is-invalid @enderror"
                                      rows="5"
                                      placeholder="اشرح المشكلة التي تواجهها مع المنتج بالتفصيل..."
                                      required>{{ old('issue_description') }}</textarea>
                            @error('issue_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">صور توضيحية (اختياري)</label>
                            <input type="file" 
                                   name="images[]" 
                                   class="form-control @error('images.*') is-invalid @enderror"
                                   multiple
                                   accept="image/*">
                            <div class="form-text">يمكنك إرفاق صور للمنتج توضح المشكلة (حتى 5 صور)</div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> إرسال الطلب
                            </button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
