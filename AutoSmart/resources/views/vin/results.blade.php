@extends('layouts.app')
@section('title', 'نتائج البحث بالشاسيه')
@section('content')
<div class="container py-4">
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <i class="bi bi-car-front display-3 text-primary"></i>
                </div>
                <div class="col-md-10">
                    <h4 class="mb-1">{{ $vinData->display_name }}</h4>
                    <p class="text-muted mb-2">رقم الشاسيه: <code class="fs-5">{{ $vinData->vin }}</code></p>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">قطع الغيار المتوافقة ({{ $products->total() }})</h5>

    @if($products->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3">لم نجد قطع متوافقة</h4>
                <p class="text-muted">جرب البحث يدويا</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">تصفح جميع المنتجات</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 product-card">
                        <div class="card-body">
                            <h6 class="card-title">{{ $product->name }}</h6>
                            <p class="text-muted small mb-2">{{ $product->store->name }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">{{ number_format($product->price, 2) }} ر.س</span>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-primary">عرض</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection
