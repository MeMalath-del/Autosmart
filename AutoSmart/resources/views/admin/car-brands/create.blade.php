@extends('layouts.admin')

@section('title', 'إضافة ماركة سيارة')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.car-brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم الماركة (إنجليزي) *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الماركة (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدولة</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country') }}" 
                                   placeholder="مثال: اليابان، ألمانيا، كوريا">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الشعار</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.car-brands.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>حفظ الماركة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
