@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>فلترة النتائج</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">التصنيف</label>
                            <select name="category" class="form-select">
                                <option value="">جميع التصنيفات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->localized_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ماركة السيارة</label>
                            <select name="brand" class="form-select">
                                <option value="">جميع الماركات</option>
                                @foreach($carBrands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->localized_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة القطعة</label>
                            <select name="condition" class="form-select">
                                <option value="">الكل</option>
                                <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="used" {{ request('condition') == 'used' ? 'selected' : '' }}>مستعمل</option>
                                <option value="refurbished" {{ request('condition') == 'refurbished' ? 'selected' : '' }}>مجدد</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">السعر</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" 
                                           placeholder="من" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" 
                                           placeholder="إلى" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="in_stock" value="1" class="form-check-input" 
                                   id="in_stock" {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="in_stock">المتوفر فقط</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>تطبيق الفلتر
                        </button>
                        
                        @if(request()->hasAny(['category', 'brand', 'condition', 'min_price', 'max_price', 'in_stock']))
                            <a href="{{ route('products.index', ['search' => request('search')]) }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="bi bi-x me-2"></i>إزالة الفلتر
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Search and Sort -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <form action="{{ route('products.index') }}" method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="ابحث عن منتج..." value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end align-items-center">
                            <span class="text-muted me-3">{{ $products->total() }} منتج</span>
                            <select class="form-select" style="width: auto;" onchange="location = this.value;">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>الأحدث</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>السعر: الأقل</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>السعر: الأعلى</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر مبيعاً</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>التقييم</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(request('search'))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    نتائج البحث عن: <strong>{{ request('search') }}</strong>
                </div>
            @endif
            
            <!-- Products -->
            @if($products->isNotEmpty())
                <div class="row g-4">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3">لا توجد نتائج</h4>
                    <p class="text-muted">جرب تغيير معايير البحث أو الفلتر</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">عرض جميع المنتجات</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
