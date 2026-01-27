@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->localized_name }}</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-body">
                    @if($product->images->isNotEmpty())
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($product->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image) }}" class="d-block w-100 rounded" 
                                             alt="{{ $product->name }}" style="max-height: 400px; object-fit: contain;">
                                    </div>
                                @endforeach
                            </div>
                            @if($product->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                                </button>
                            @endif
                        </div>
                        
                        @if($product->images->count() > 1)
                            <div class="d-flex gap-2 mt-3 overflow-auto">
                                @foreach($product->images as $index => $image)
                                    <img src="{{ asset('storage/' . $image->image) }}" 
                                         class="rounded cursor-pointer border" 
                                         style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                                         onclick="document.querySelector('#productCarousel').querySelector('.carousel-item.active').classList.remove('active'); document.querySelectorAll('#productCarousel .carousel-item')[{{ $index }}].classList.add('active');">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <img src="{{ asset('images/no-image.png') }}" class="d-block w-100 rounded" alt="No Image">
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="condition-badge condition-{{ $product->condition }} mb-2 d-inline-block">
                                @switch($product->condition)
                                    @case('new') جديد @break
                                    @case('used') مستعمل @break
                                    @case('refurbished') مجدد @break
                                @endswitch
                            </span>
                            <h2 class="mb-2">{{ $product->localized_name }}</h2>
                        </div>
                        @livewire('shop.wishlist', ['product' => $product])
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        @if($product->rating > 0)
                            <div class="rating me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $product->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted">({{ $product->rating_count }} تقييم)</span>
                        @endif
                        <span class="text-muted ms-3"><i class="bi bi-eye me-1"></i>{{ $product->views }} مشاهدة</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        @if($product->sale_price)
                            <span class="product-old-price fs-5">{{ number_format($product->price, 2) }} ر.س</span>
                            <span class="badge bg-danger ms-2">-{{ $product->discount_percentage }}%</span>
                        @endif
                        <div class="product-price fs-2">{{ number_format($product->current_price, 2) }} ر.س</div>
                        <small class="text-muted">شامل الضريبة</small>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="mb-4">
                        @if($product->isInStock())
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>متوفر في المخزون ({{ $product->quantity }})</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>غير متوفر</span>
                        @endif
                    </div>
                    
                    <!-- Add to Cart -->
                    @livewire('shop.add-to-cart', ['product' => $product])
                    
                    <hr class="my-4">
                    
                    <!-- Product Details -->
                    <div class="row g-3">
                        @if($product->part_number)
                            <div class="col-6">
                                <strong>رقم القطعة:</strong><br>
                                <span class="text-muted">{{ $product->part_number }}</span>
                            </div>
                        @endif
                        @if($product->oem_number)
                            <div class="col-6">
                                <strong>رقم OEM:</strong><br>
                                <span class="text-muted">{{ $product->oem_number }}</span>
                            </div>
                        @endif
                        @if($product->brand)
                            <div class="col-6">
                                <strong>العلامة التجارية:</strong><br>
                                <span class="text-muted">{{ $product->brand }}</span>
                            </div>
                        @endif
                        @if($product->warranty !== 'none')
                            <div class="col-6">
                                <strong>الضمان:</strong><br>
                                <span class="text-muted">
                                    @switch($product->warranty)
                                        @case('3_months') 3 أشهر @break
                                        @case('6_months') 6 أشهر @break
                                        @case('1_year') سنة @break
                                        @case('2_years') سنتان @break
                                    @endswitch
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Compatible Cars -->
                    @if($product->carModels->isNotEmpty())
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="bi bi-car-front me-2"></i>يتوافق مع:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->carModels as $carModel)
                                <span class="badge bg-light text-dark border">
                                    {{ $carModel->brand->name }} {{ $carModel->name }}
                                    @if($carModel->pivot->year_from || $carModel->pivot->year_to)
                                        ({{ $carModel->pivot->year_from ?? '...' }} - {{ $carModel->pivot->year_to ?? '...' }})
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Store Info -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @if($product->store->logo)
                            <img src="{{ asset('storage/' . $product->store->logo) }}" class="rounded-circle me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; font-size: 1.5rem;">
                                {{ mb_substr($product->store->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('stores.show', $product->store->slug) }}" class="text-dark text-decoration-none">
                                    {{ $product->store->localized_name }}
                                </a>
                                @if($product->store->is_verified)
                                    <i class="bi bi-patch-check-fill text-primary"></i>
                                @endif
                            </h6>
                            <div class="rating small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $product->store->rating ? '-fill' : '' }}"></i>
                                @endfor
                                <span class="text-muted">({{ $product->store->rating_count }})</span>
                            </div>
                        </div>
                        <a href="{{ route('stores.show', $product->store->slug) }}" class="btn btn-outline-primary">
                            زيارة المتجر
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Description -->
    @if($product->localized_description)
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">وصف المنتج</h5>
            </div>
            <div class="card-body">
                {!! nl2br(e($product->localized_description)) !!}
            </div>
        </div>
    @endif
    
    <!-- Reviews -->
    <div class="card mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">التقييمات ({{ $product->reviews->count() }})</h5>
        </div>
        <div class="card-body">
            @forelse($product->reviews->where('is_approved', true) as $review)
                <div class="d-flex mb-4">
                    <img src="{{ $review->user->avatar_url }}" class="rounded-circle me-3" 
                         style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>{{ $review->user->name }}</strong>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="rating small mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                            @if($review->is_verified_purchase)
                                <span class="badge bg-success ms-2">مشتري موثق</span>
                            @endif
                        </div>
                        @if($review->comment)
                            <p class="mb-0">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
                @if(!$loop->last)<hr>@endif
            @empty
                <p class="text-center text-muted py-4">لا توجد تقييمات بعد</p>
            @endforelse
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5">
            <h4 class="mb-4">منتجات مشابهة</h4>
            <div class="row g-4">
                @foreach($relatedProducts as $relatedProduct)
                    @include('components.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
