@extends('layouts.app')

@section('title', 'نتائج البحث بالصور')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ Storage::url($result->image_path) }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <p class="mb-2">
                        <strong>نسبة الثقة:</strong>
                        <span class="badge bg-{{ $result->confidence_score > 0.7 ? 'success' : ($result->confidence_score > 0.4 ? 'warning' : 'danger') }}">
                            {{ number_format($result->confidence_score * 100, 0) }}%
                        </span>
                    </p>
                    <p class="text-muted mb-0">تم العثور على {{ $result->results_count }} نتيجة</p>
                </div>
            </div>
            
            <a href="{{ route('search.image') }}" class="btn btn-outline-primary w-100 mt-3">
                <i class="bi bi-arrow-right me-2"></i>بحث جديد
            </a>
        </div>
        
        <div class="col-lg-9">
            <h2 class="mb-4">نتائج البحث</h2>
            
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 product-card">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    <p class="product-price mb-0">{{ number_format($product->price, 2) }} ريال</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3">لم يتم العثور على نتائج</h4>
                    <p class="text-muted">جرب استخدام صورة أخرى أو البحث النصي</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        تصفح المنتجات
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
