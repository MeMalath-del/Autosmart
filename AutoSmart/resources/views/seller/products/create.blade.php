@extends('layouts.seller')

@section('title', 'إضافة منتج')

@section('content')
<form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
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
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم المنتج (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التصنيف *</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">حالة القطعة *</label>
                            <select name="condition" class="form-select" required>
                                <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>مستعمل</option>
                                <option value="refurbished" {{ old('condition') == 'refurbished' ? 'selected' : '' }}>مجدد</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف (إنجليزي)</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف (عربي)</label>
                            <textarea name="description_ar" class="form-control" rows="4">{{ old('description_ar') }}</textarea>
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
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم القطعة</label>
                            <input type="text" name="part_number" class="form-control" value="{{ old('part_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم OEM</label>
                            <input type="text" name="oem_number" class="form-control" value="{{ old('oem_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">العلامة التجارية</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الشركة المصنعة</label>
                            <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الضمان *</label>
                            <select name="warranty" class="form-select" required>
                                <option value="none">بدون ضمان</option>
                                <option value="3_months">3 أشهر</option>
                                <option value="6_months">6 أشهر</option>
                                <option value="1_year">سنة</option>
                                <option value="2_years">سنتان</option>
                            </select>
                        </div>
                    </div>
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
                                                   {{ in_array($model->id, old('car_models', [])) ? 'checked' : '' }}>
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
            
            <!-- Images -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">صور المنتج</h5>
                </div>
                <div class="card-body">
                    <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror" 
                           multiple accept="image/*">
                    <small class="text-muted">يمكنك رفع حتى 5 صور. الصورة الأولى ستكون الصورة الرئيسية.</small>
                    @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price') }}" required>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سعر التخفيض (ر.س)</label>
                        <input type="number" name="sale_price" step="0.01" class="form-control" value="{{ old('sale_price') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكمية المتوفرة *</label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity', 0) }}" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-check-lg me-2"></i>حفظ المنتج
                    </button>
                    <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary w-100 mt-2">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
