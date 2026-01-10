@extends('layouts.app')

@section('title', $store->name)

@section('content')
<!-- Store Header -->
<div class="position-relative">
    @if($store->banner)
        <img src="{{ asset('storage/' . $store->banner) }}" class="w-100" style="height: 250px; object-fit: cover;">
    @else
        <div class="bg-primary" style="height: 250px;"></div>
    @endif
</div>

<div class="container">
    <div class="card" style="margin-top: -80px; position: relative; z-index: 10;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                    @if($store->logo)
                        <img src="{{ asset('storage/' . $store->logo) }}" class="rounded-circle border border-4 border-white shadow" 
                             style="width: 120px; height: 120px; object-fit: cover; margin-top: -60px;">
                    @else
                        <div class="rounded-circle border border-4 border-white shadow bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; font-size: 3rem; margin-top: -60px;">
                            {{ mb_substr($store->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="col-md text-center text-md-start">
                    <h2 class="mb-1">
                        {{ $store->localized_name }}
                        @if($store->is_verified)
                            <i class="bi bi-patch-check-fill text-primary"></i>
                        @endif
                    </h2>
                    <div class="rating mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $store->rating ? '-fill' : '' }}"></i>
                        @endfor
                        <span class="text-muted">({{ $store->rating_count }} تقييم)</span>
                    </div>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt me-1"></i>{{ $store->city }}، {{ $store->country }}
                    </p>
                </div>
                <div class="col-md-auto text-center mt-3 mt-md-0">
                    @if($store->whatsapp)
                        <a href="https://wa.me/{{ $store->whatsapp }}" class="btn btn-success" target="_blank">
                            <i class="bi bi-whatsapp me-2"></i>واتساب
                        </a>
                    @endif
                    @if($store->phone)
                        <a href="tel:{{ $store->phone }}" class="btn btn-outline-primary">
                            <i class="bi bi-telephone me-2"></i>اتصال
                        </a>
                    @endif
                </div>
            </div>
            
            @if($store->description)
                <hr>
                <p class="mb-0">{{ $store->description }}</p>
            @endif
        </div>
    </div>
    
    <!-- Products -->
    <div class="mt-4">
        <h4 class="mb-4">منتجات المتجر ({{ $products->total() }})</h4>
        
        @if($products->isNotEmpty())
            <div class="row g-4">
                @foreach($products as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">لا توجد منتجات بعد</h5>
            </div>
        @endif
    </div>
</div>
@endsection
