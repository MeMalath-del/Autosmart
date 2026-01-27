@extends('layouts.admin')

@section('title', 'تعديل ماركة: ' . $carBrand->name)

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">تعديل الماركة</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.car-brands.update', $carBrand) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">اسم الماركة (إنجليزي) *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $carBrand->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">اسم الماركة (عربي)</label>
                        <input type="text" name="name_ar" class="form-control" 
                               value="{{ old('name_ar', $carBrand->name_ar) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الدولة</label>
                        <input type="text" name="country" class="form-control" 
                               value="{{ old('country', $carBrand->country) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الشعار</label>
                        @if($carBrand->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $carBrand->logo) }}" style="max-height: 60px;">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                               {{ old('is_active', $carBrand->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>حفظ التغييرات
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">الموديلات ({{ $carBrand->models->count() }})</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.car-brands.models.store', $carBrand) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="اسم الموديل *" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="name_ar" class="form-control" placeholder="بالعربي">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="year_from" class="form-control" placeholder="من">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="year_to" class="form-control" placeholder="إلى">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>الموديل</th>
                                <th>السنوات</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carBrand->models as $model)
                                <tr>
                                    <td>{{ $model->name }} @if($model->name_ar)({{ $model->name_ar }})@endif</td>
                                    <td>
                                        @if($model->year_from || $model->year_to)
                                            {{ $model->year_from ?? '...' }} - {{ $model->year_to ?? '...' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.car-models.destroy', $model) }}" method="POST"
                                              onsubmit="return confirm('حذف هذا الموديل؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">لا توجد موديلات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
