@extends('layouts.seller')
@section('title', 'إنشاء مزاد جديد')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-hammer me-2"></i>إنشاء مزاد جديد</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('seller.auctions.store') }}" method="POST">@csrf
                    <div class="mb-3">
                        <label class="form-label">المنتج</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- اختر منتجاً --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->price, 2) }} ر.س)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">عنوان المزاد</label>
                        <input type="text" name="title" class="form-control" required placeholder="عنوان جذاب للمزاد">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="تفاصيل إضافية عن المزاد..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">السعر الابتدائي</label>
                            <div class="input-group"><input type="number" name="starting_price" class="form-control" step="0.01" required><span class="input-group-text">ر.س</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الحد الأدنى للمزايدة</label>
                            <div class="input-group"><input type="number" name="bid_increment" class="form-control" step="0.01" value="10" required><span class="input-group-text">ر.س</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">السعر الاحتياطي (اختياري)</label>
                            <div class="input-group"><input type="number" name="reserve_price" class="form-control" step="0.01"><span class="input-group-text">ر.س</span></div>
                            <div class="form-text">لن يباع إذا لم يصل لهذا السعر</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سعر الشراء الفوري (اختياري)</label>
                            <div class="input-group"><input type="number" name="buy_now_price" class="form-control" step="0.01"><span class="input-group-text">ر.س</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">يبدأ في</label>
                            <input type="datetime-local" name="starts_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ينتهي في</label>
                            <input type="datetime-local" name="ends_at" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">إنشاء المزاد</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
