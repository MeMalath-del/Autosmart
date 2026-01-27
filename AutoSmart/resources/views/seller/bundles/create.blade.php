@extends('layouts.seller')
@section('title', 'إنشاء باقة')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-box2-heart me-2"></i>إنشاء باقة جديدة</h1>

<form action="{{ route('seller.bundles.store') }}" method="POST" enctype="multipart/form-data">@csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">معلومات الباقة</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم الباقة (عربي)</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الباقة (إنجليزي)</label>
                            <input type="text" name="name_ar" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">صورة الباقة</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سعر الباقة</label>
                            <div class="input-group"><input type="number" step="0.01" name="bundle_price" class="form-control" required><span class="input-group-text">ر.س</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الكمية المتوفرة</label>
                            <input type="number" name="quantity" class="form-control" value="100" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">منتجات الباقة</h5>
                    <button type="button" class="btn btn-sm btn-success" onclick="addProduct()"><i class="bi bi-plus-lg"></i></button>
                </div>
                <div class="card-body" id="productsContainer">
                    <div class="row g-3 mb-3 product-row">
                        <div class="col-md-8">
                            <select name="products[0][product_id]" class="form-select" required>
                                <option value="">-- اختر منتج --</option>
                                @foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->current_price, 2) }} ر.س)</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><input type="number" name="products[0][quantity]" class="form-control" value="1" min="1" placeholder="الكمية"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-outline-danger" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top:100px;">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg w-100">إنشاء الباقة</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let productIndex = 1;
function addProduct() {
    const html = `<div class="row g-3 mb-3 product-row">
        <div class="col-md-8"><select name="products[${productIndex}][product_id]" class="form-select" required>
            <option value="">-- اختر منتج --</option>
            @foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach
        </select></div>
        <div class="col-md-3"><input type="number" name="products[${productIndex}][quantity]" class="form-control" value="1" min="1"></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button></div>
    </div>`;
    document.getElementById('productsContainer').insertAdjacentHTML('beforeend', html);
    productIndex++;
}
function removeProduct(btn) { if(document.querySelectorAll('.product-row').length > 1) btn.closest('.product-row').remove(); }
</script>
@endsection
