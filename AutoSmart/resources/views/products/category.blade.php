@extends('layouts.app')

@section('title', $category->localized_name)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
            <li class="breadcrumb-item active">{{ $category->localized_name }}</li>
        </ol>
    </nav>
    
    <h2 class="mb-4">{{ $category->localized_name }}</h2>
    
    @if($category->description)
        <p class="text-muted mb-4">{{ $category->description }}</p>
    @endif
    
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
            <h4 class="mt-3">لا توجد منتجات في هذا التصنيف</h4>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">تصفح جميع المنتجات</a>
        </div>
    @endif
</div>
@endsection
