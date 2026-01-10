@extends('layouts.app')

@section('title', 'المتاجر')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-shop me-2"></i>المتاجر</h2>
    
    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="ابحث عن متجر..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="city" class="form-select">
                        <option value="">جميع المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">بحث</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Stores Grid -->
    @if($stores->isNotEmpty())
        <div class="row g-4">
            @foreach($stores as $store)
                <div class="col-md-4">
                    <div class="card store-card h-100">
                        @if($store->banner)
                            <img src="{{ asset('storage/' . $store->banner) }}" class="card-img-top" 
                                 style="height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-primary" style="height: 120px;"></div>
                        @endif
                        <div class="card-body text-center" style="margin-top: -50px;">
                            @if($store->logo)
                                <img src="{{ asset('storage/' . $store->logo) }}" class="store-logo">
                            @else
                                <div class="store-logo bg-white text-primary d-flex align-items-center justify-content-center mx-auto border" 
                                     style="font-size: 2rem; font-weight: bold;">
                                    {{ mb_substr($store->name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="mt-2 mb-1">
                                {{ $store->localized_name }}
                                @if($store->is_verified)
                                    <i class="bi bi-patch-check-fill text-primary"></i>
                                @endif
                            </h5>
                            <div class="rating mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $store->rating ? '-fill' : '' }}"></i>
                                @endfor
                                <span class="text-muted small">({{ $store->rating_count }})</span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-geo-alt me-1"></i>{{ $store->city }}
                            </p>
                            <p class="text-muted small mb-3">{{ $store->products_count }} منتج</p>
                            <a href="{{ route('stores.show', $store->slug) }}" class="btn btn-outline-primary btn-sm">
                                زيارة المتجر
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $stores->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-shop display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد متاجر</h4>
        </div>
    @endif
</div>
@endsection
