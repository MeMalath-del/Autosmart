@extends('layouts.app')

@section('title', 'طلب قطعة غيار')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">طلب قطعة غيار</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('part-requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-1"></i>
                            اشرح القطعة التي تبحث عنها وسيتواصل معك البائعون بعروضهم
                        </div>

                        <h6 class="mb-3">معلومات السيارة</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ماركة السيارة</label>
                                <select name="car_brand_id" class="form-select" id="carBrand">
                                    <option value="">اختر الماركة</option>
                                    @foreach($carBrands as $brand)
                                        <option value="{{ $brand->id }}" data-models='@json($brand->models)'>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">موديل السيارة</label>
                                <select name="car_model_id" class="form-select" id="carModel">
                                    <option value="">اختر الموديل</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">سنة الصنع</label>
                                <select name="car_year" class="form-select">
                                    <option value="">اختر السنة</option>
                                    @for($year = date('Y') + 1; $year >= 1990; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <h6 class="mb-3">معلومات القطعة</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">اسم القطعة <span class="text-danger">*</span></label>
                                <input type="text" name="part_name" 
                                       class="form-control @error('part_name') is-invalid @enderror"
                                       value="{{ old('part_name') }}"
                                       placeholder="مثال: فلتر زيت، بطارية، مكينة..."
                                       required>
                                @error('part_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم القطعة (إن وجد)</label>
                                <input type="text" name="part_number" class="form-control"
                                       value="{{ old('part_number') }}"
                                       placeholder="OEM / Part Number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الأولوية</label>
                                <select name="urgency" class="form-select" required>
                                    <option value="low">عادي</option>
                                    <option value="medium">متوسط</option>
                                    <option value="high">عاجل</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف تفصيلي</label>
                                <textarea name="description" class="form-control" rows="4"
                                          placeholder="اشرح القطعة المطلوبة بالتفصيل، أي مواصفات إضافية...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <h6 class="mb-3">الميزانية (اختياري)</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">من (ر.س)</label>
                                <input type="number" name="budget_min" class="form-control" min="0" step="1"
                                       value="{{ old('budget_min') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">إلى (ر.س)</label>
                                <input type="number" name="budget_max" class="form-control" min="0" step="1"
                                       value="{{ old('budget_max') }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">صور توضيحية (اختياري)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <div class="form-text">يمكنك إرفاق صور للقطعة المطلوبة</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> نشر الطلب
                            </button>
                            <a href="{{ route('part-requests.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('carBrand').addEventListener('change', function() {
    const modelSelect = document.getElementById('carModel');
    modelSelect.innerHTML = '<option value="">اختر الموديل</option>';
    
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        const models = JSON.parse(selected.dataset.models);
        models.forEach(model => {
            modelSelect.innerHTML += `<option value="${model.id}">${model.name}</option>`;
        });
    }
});
</script>
@endsection
