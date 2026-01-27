@extends('layouts.admin')

@section('title', 'إضافة تصنيف')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم التصنيف (إنجليزي) *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم التصنيف (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التصنيف الأب</label>
                            <select name="parent_id" class="form-select">
                                <option value="">بدون (تصنيف رئيسي)</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الترتيب</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">صورة التصنيف</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>حفظ التصنيف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
