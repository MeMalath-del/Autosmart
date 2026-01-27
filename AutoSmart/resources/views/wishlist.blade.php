@extends('layouts.app')

@section('title', 'المفضلة')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-heart me-2"></i>المفضلة</h2>
    
    @php
        $wishlistProducts = auth()->user()->wishlistProducts()->with(['store', 'category', 'images'])->get();
    @endphp
    
    @if($wishlistProducts->isNotEmpty())
        <div class="row g-4">
            @foreach($wishlistProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-heart display-1 text-muted"></i>
            <h4 class="mt-3">قائمة المفضلة فارغة</h4>
            <p class="text-muted">لم تقم بإضافة أي منتجات للمفضلة بعد</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">تصفح المنتجات</a>
        </div>
    @endif
</div>
@endsection
