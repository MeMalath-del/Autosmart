@extends('layouts.seller')

@section('title', 'تعديل المنتج')

@section('content')
<form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">المعلومات الأساسية</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم المنتج (إنجليزي) *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم المنتج (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" 
                                   value="{{ old('name_ar', $product->name_ar) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التصنيف *</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">حالة القطعة *</label>
                            <select name="condition" class="form-select" required>
                                <option value="new" {{ $product->condition == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="used" {{ $product->condition == 'used' ? 'selected' : '' }}>مستعمل</option>
                                <option value="refurbished" {{ $product->condition == 'refurbished' ? 'selected' : '' }}>مجدد</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف (إنجليزي)</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف (عربي)</label>
                            <textarea name="description_ar" class="form-control" rows="4">{{ old('description_ar', $product->description_ar) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">تفاصيل المنتج</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">رقم SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم القطعة</label>
                            <input type="text" name="part_number" class="form-control" value="{{ old('part_number', $product->part_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم OEM</label>
                            <input type="text" name="oem_number" class="form-control" value="{{ old('oem_number', $product->oem_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">العلامة التجارية</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الشركة المصنعة</label>
                            <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $product->manufacturer) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الضمان</label>
                            <select name="warranty" class="form-select">
                                <option value="none" {{ $product->warranty == 'none' ? 'selected' : '' }}>بدون ضمان</option>
                                <option value="3_months" {{ $product->warranty == '3_months' ? 'selected' : '' }}>3 أشهر</option>
                                <option value="6_months" {{ $product->warranty == '6_months' ? 'selected' : '' }}>6 أشهر</option>
                                <option value="1_year" {{ $product->warranty == '1_year' ? 'selected' : '' }}>سنة</option>
                                <option value="2_years" {{ $product->warranty == '2_years' ? 'selected' : '' }}>سنتان</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Current Images -->
            @if($product->images->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">الصور الحالية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($product->images as $image)
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid rounded">
                                        <form action="{{ route('seller.products.images.delete', $image) }}" method="POST" 
                                              class="position-absolute top-0 end-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('حذف الصورة؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @if($image->is_primary)
                                            <span class="badge bg-primary position-absolute bottom-0 start-0 m-1">رئيسية</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Add Images -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">إضافة صور</h5>
                </div>
                <div class="card-body">
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">يمكنك رفع حتى 5 صور</small>
                </div>
            </div>
            
            <!-- Compatible Cars -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">السيارات المتوافقة</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($carBrands as $brand)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="mb-2">{{ $brand->name }}</h6>
                                    @foreach($brand->models as $model)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="car_models[]" 
                                                   value="{{ $model->id }}" id="model{{ $model->id }}"
                                                   {{ $product->carModels->contains($model->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="model{{ $model->id }}">
                                                {{ $model->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">التسعير والمخزون</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">السعر (ر.س) *</label>
                        <input type="number" name="price" step="0.01" class="form-control" 
                               value="{{ old('price', $product->price) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سعر التخفيض (ر.س)</label>
                        <input type="number" name="sale_price" step="0.01" class="form-control" 
                               value="{{ old('sale_price', $product->sale_price) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكمية المتوفرة *</label>
                        <input type="number" name="quantity" class="form-control" 
                               value="{{ old('quantity', $product->quantity) }}" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                               {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط (مرئي للعملاء)</label>
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">إحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>المشاهدات:</span>
                        <span class="fw-bold">{{ $product->views }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>المبيعات:</span>
                        <span class="fw-bold">{{ $product->sales_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>التقييم:</span>
                        <span class="fw-bold">
                            <i class="bi bi-star-fill text-warning"></i>
                            {{ number_format($product->rating, 1) }} ({{ $product->rating_count }})
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-check-lg me-2"></i>حفظ التغييرات
                    </button>
                    <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary w-100 mt-2">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
