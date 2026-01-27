@extends('layouts.app')

@section('title', 'مقارنة المنتجات')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">مقارنة المنتجات</h1>

    @if($comparison->products->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-columns-gap display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد منتجات للمقارنة</h4>
            <p class="text-muted">أضف منتجات للمقارنة من صفحات المنتجات</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">تصفح المنتجات</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered comparison-table">
                <thead>
                    <tr>
                        <th style="width: 200px;">المنتج</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center" style="min-width: 250px;">
                                <div class="position-relative">
                                    <form action="{{ route('compare.remove', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                    <img src="{{ $product->main_image }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-fluid mb-2"
                                         style="max-height: 150px;">
                                    <h6 class="mb-0">
                                        <a href="{{ route('products.show', $product) }}" class="text-dark">
                                            {{ $product->localized_name }}
                                        </a>
                                    </h6>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>السعر</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                @if($product->is_on_sale)
                                    <span class="text-danger fw-bold">{{ number_format($product->sale_price, 2) }} ر.س</span>
                                    <br>
                                    <small class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }} ر.س</small>
                                @else
                                    <span class="fw-bold">{{ number_format($product->price, 2) }} ر.س</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>التصنيف</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">{{ $product->category?->localized_name ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>المتجر</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                <a href="{{ route('stores.show', $product->store) }}">
                                    {{ $product->store?->localized_name }}
                                </a>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>الحالة</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                <span class="badge bg-{{ $product->condition === 'new' ? 'success' : ($product->condition === 'used' ? 'warning' : 'info') }}">
                                    {{ $product->condition_label }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>التوفر</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                @if($product->isInStock())
                                    <span class="text-success"><i class="bi bi-check-circle"></i> متوفر</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle"></i> غير متوفر</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>التقييم</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $product->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">({{ $product->reviews_count ?? 0 }} تقييم)</small>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>الضمان</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">{{ $product->warranty ?? 'لا يوجد' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>رقم القطعة</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">{{ $product->part_number ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>العلامة التجارية</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">{{ $product->brand ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>السيارات المتوافقة</th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                @if($product->carModels->isNotEmpty())
                                    @foreach($product->carModels->take(3) as $model)
                                        <span class="badge bg-light text-dark mb-1">{{ $model->brand->name }} {{ $model->name }}</span>
                                    @endforeach
                                    @if($product->carModels->count() > 3)
                                        <span class="text-muted">+{{ $product->carModels->count() - 3 }}</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th></th>
                        @foreach($comparison->products as $product)
                            <td class="text-center">
                                @if($product->isInStock())
                                    <livewire:shop.add-to-cart :product="$product" :key="'compare-cart-'.$product->id" />
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <form action="{{ route('compare.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-trash me-1"></i> مسح المقارنة
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
