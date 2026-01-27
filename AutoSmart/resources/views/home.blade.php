@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-5 fw-bold mb-3">ابحث عن قطع غيار سيارتك بسهولة</h1>
                <p class="lead mb-4">منصة موثوقة تربطك بأفضل موردي قطع الغيار الأصلية والبديلة في المملكة</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg">
                        <i class="bi bi-search me-2"></i>تصفح المنتجات
                    </a>
                    <a href="{{ route('register') }}?role=seller" class="btn btn-outline-light btn-lg">
                        انضم كبائع
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="search-box">
                    @livewire('shop.product-search')
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">تصفح حسب التصنيف</h2>
            <a href="{{ route('products.index') }}" class="text-primary">عرض الكل <i class="bi bi-arrow-left"></i></a>
        </div>
        <div class="row g-3">
            @forelse($categories as $category)
                <div class="col-6 col-md-3">
                    <a href="{{ route('products.category', $category->slug) }}" class="text-decoration-none">
                        <div class="category-card card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-gear-wide-connected text-primary"></i>
                                <h6 class="mt-2 mb-1">{{ $category->localized_name }}</h6>
                                <small class="text-muted">{{ $category->products_count }} منتج</small>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1"></i>
                    <p>لا توجد تصنيفات بعد</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->isNotEmpty())
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-star-fill text-warning me-2"></i>منتجات مميزة
            </h2>
            <a href="{{ route('products.index') }}?featured=1" class="text-primary">عرض الكل <i class="bi bi-arrow-left"></i></a>
        </div>
        <div class="row g-4">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Car Brands -->
@if($carBrands->isNotEmpty())
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4 text-center">ماركات السيارات</h2>
        <div class="row g-3 justify-content-center">
            @foreach($carBrands->take(12) as $brand)
                <div class="col-4 col-md-2">
                    <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="text-decoration-none">
                        <div class="card h-100 text-center py-3">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" 
                                     class="mx-auto" style="height: 40px; object-fit: contain;">
                            @else
                                <i class="bi bi-car-front fs-2 text-primary"></i>
                            @endif
                            <small class="mt-2 text-dark">{{ $brand->localized_name }}</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Products -->
@if($latestProducts->isNotEmpty())
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">أحدث المنتجات</h2>
            <a href="{{ route('products.index') }}" class="text-primary">عرض الكل <i class="bi bi-arrow-left"></i></a>
        </div>
        <div class="row g-4">
            @foreach($latestProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Stores -->
@if($featuredStores->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">متاجر موثوقة</h2>
            <a href="{{ route('stores.index') }}" class="text-primary">عرض الكل <i class="bi bi-arrow-left"></i></a>
        </div>
        <div class="row g-4">
            @foreach($featuredStores as $store)
                <div class="col-md-4 col-lg-2">
                    <a href="{{ route('stores.show', $store->slug) }}" class="text-decoration-none">
                        <div class="card store-card h-100 py-3">
                            <div class="card-body text-center">
                                @if($store->logo)
                                    <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="store-logo">
                                @else
                                    <div class="store-logo bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="font-size: 2rem;">
                                        {{ mb_substr($store->name, 0, 1) }}
                                    </div>
                                @endif
                                <h6 class="mb-1 text-dark">
                                    {{ $store->localized_name }}
                                    @if($store->is_verified)
                                        <i class="bi bi-patch-check-fill verified-badge"></i>
                                    @endif
                                </h6>
                                <div class="rating small mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $store->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                    <span class="text-muted">({{ $store->rating_count }})</span>
                                </div>
                                <small class="text-muted">{{ $store->products_count }} منتج</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <h2 class="fw-bold text-center mb-5">لماذا AutoSmart؟</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-shield-check fs-1"></i>
                </div>
                <h5>منتجات موثوقة</h5>
                <p class="opacity-75">جميع المنتجات من موردين معتمدين وموثوقين</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-truck fs-1"></i>
                </div>
                <h5>توصيل سريع</h5>
                <p class="opacity-75">شحن سريع لجميع مناطق المملكة</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-currency-dollar fs-1"></i>
                </div>
                <h5>أسعار منافسة</h5>
                <p class="opacity-75">أفضل الأسعار مع ضمان الجودة</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-headset fs-1"></i>
                </div>
                <h5>دعم فني</h5>
                <p class="opacity-75">فريق دعم متخصص لمساعدتك</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold">هل أنت بائع قطع غيار؟</h3>
                <p class="text-muted mb-0">انضم لآلاف البائعين وابدأ في بيع منتجاتك عبر منصتنا</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('register') }}?role=seller" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop me-2"></i>سجل كبائع الآن
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
