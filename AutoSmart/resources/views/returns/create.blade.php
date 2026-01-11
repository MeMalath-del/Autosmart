@extends('layouts.app')
@section('title', 'طلب إرجاع')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-arrow-counterclockwise me-2"></i>طلب إرجاع - الطلب #{{ $order->order_number }}</h1>

    <form action="{{ route('returns.store', $order) }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">اختر المنتجات للإرجاع</h5></div>
                    <div class="card-body">
                        @foreach($order->items as $item)
                            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                <input type="checkbox" name="items[{{ $loop->index }}][order_item_id]" value="{{ $item->id }}" class="form-check-input me-3">
                                <img src="{{ $item->product->primary_image_url }}" class="rounded" style="width:60px;height:60px;object-fit:cover;">
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="mb-0">{{ $item->product->name }}</h6>
                                    <small class="text-muted">الكمية: {{ $item->quantity }} × {{ number_format($item->price, 2) }} ر.س</small>
                                </div>
                                <div style="width:100px;">
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control form-control-sm" value="1" min="1" max="{{ $item->quantity }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">تفاصيل الإرجاع</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">نوع الطلب</label>
                                <select name="type" class="form-select" required>
                                    <option value="return">إرجاع واسترداد المبلغ</option>
                                    <option value="exchange">استبدال بمنتج آخر</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">سبب الإرجاع</label>
                                <select name="reason" class="form-select" required>
                                    <option value="defective">المنتج معيب</option>
                                    <option value="wrong_item">استلمت منتجاً خاطئاً</option>
                                    <option value="not_as_described">لا يطابق الوصف</option>
                                    <option value="changed_mind">غيرت رأيي</option>
                                    <option value="other">سبب آخر</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف المشكلة</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="اشرح المشكلة بالتفصيل..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">صور المنتج (اختياري)</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                <div class="form-text">يمكنك رفع صور توضح حالة المنتج</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top:100px;">
                    <div class="card-header"><h5 class="mb-0">سياسة الإرجاع</h5></div>
                    <div class="card-body">
                        <ul class="mb-4">
                            <li>الإرجاع خلال 14 يوماً من الاستلام</li>
                            <li>المنتج بحالته الأصلية</li>
                            <li>مع جميع الملحقات والتغليف</li>
                            <li>الاسترداد خلال 5-7 أيام عمل</li>
                        </ul>
                        <button type="submit" class="btn btn-primary w-100">تقديم طلب الإرجاع</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
