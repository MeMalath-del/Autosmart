@extends('layouts.seller')

@section('title', 'المنتجات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="{{ route('seller.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>إضافة منتج
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>نفذ المخزون</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">بحث</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المنتج</th>
                        <th>التصنيف</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>الحالة</th>
                        <th>المبيعات</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $product->primary_image_url }}" class="rounded me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ Str::limit($product->name, 35) }}</div>
                                        @if($product->sku)
                                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                @if($product->sale_price)
                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }}</span><br>
                                @endif
                                <span class="fw-bold">{{ number_format($product->current_price, 2) }} ر.س</span>
                            </td>
                            <td>
                                @if($product->quantity == 0)
                                    <span class="text-danger">نفذ</span>
                                @elseif($product->quantity <= 5)
                                    <span class="text-warning">{{ $product->quantity }}</span>
                                @else
                                    {{ $product->quantity }}
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>{{ $product->sales_count }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('seller.products.destroy', $product) }}" method="POST" 
                                          onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-box-seam fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">لا توجد منتجات</p>
                                <a href="{{ route('seller.products.create') }}" class="btn btn-primary">إضافة منتج</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection
