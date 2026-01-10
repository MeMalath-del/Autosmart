@extends('layouts.app')
@section('title', $productList->name)
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('lists.index') }}" class="text-decoration-none"><i class="bi bi-arrow-right me-1"></i>العودة للقوائم</a>
            <h1 class="h3 mb-0 mt-2">{{ $productList->name }}</h1>
            @if($productList->description)<p class="text-muted mb-0">{{ $productList->description }}</p>@endif
        </div>
        <div class="d-flex gap-2">
            @if($productList->is_public)
                <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ route('lists.shared', $productList->share_token) }}'); alert('تم نسخ الرابط!');"><i class="bi bi-share me-1"></i> مشاركة</button>
            @endif
            <form action="{{ route('lists.cart', $productList) }}" method="POST">@csrf
                <button class="btn btn-primary"><i class="bi bi-cart-plus me-1"></i> إضافة الكل للسلة</button>
            </form>
        </div>
    </div>

    @if($productList->items->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-box display-1 text-muted"></i>
            <h4 class="mt-3">القائمة فارغة</h4>
            <p class="text-muted">أضف منتجات من صفحات المنتجات</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">تصفح المنتجات</a>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>المنتج</th><th>السعر</th><th>الكمية</th><th>الإجمالي</th><th></th></tr></thead>
                    <tbody>
                        @foreach($productList->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product->images->first())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->path) }}" class="me-3" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                        @endif
                                        <div><a href="{{ route('products.show', $item->product) }}" class="text-decoration-none">{{ $item->product->localized_name }}</a></div>
                                    </div>
                                </td>
                                <td>{{ number_format($item->product->current_price, 2) }} ر.س</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->product->current_price * $item->quantity, 2) }} ر.س</td>
                                <td>
                                    <form action="{{ route('lists.remove', [$productList, $item->product_id]) }}" method="POST">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light"><th colspan="3">الإجمالي</th><th colspan="2">{{ number_format($productList->total, 2) }} ر.س</th></tr>
                    </tfoot>
                </table></div>
            </div>
        </div>
    @endif
</div>
@endsection
