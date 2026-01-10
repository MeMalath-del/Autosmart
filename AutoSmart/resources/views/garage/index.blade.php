@extends('layouts.app')
@section('title', 'سياراتي')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-car-front me-2"></i>سياراتي</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
            <i class="bi bi-plus-lg me-1"></i> إضافة سيارة
        </button>
    </div>

    @if($cars->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-car-front display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد سيارات مسجلة</h4>
            <p class="text-muted">أضف سيارتك للحصول على توصيات مخصصة لقطع الغيار</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">إضافة سيارة</button>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($cars as $car)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 {{ $car->is_primary ? 'border-primary' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $car->display_name }}</h5>
                                    <p class="text-muted small mb-0">{{ $car->brand?->name }} {{ $car->model?->name }}</p>
                                </div>
                                @if($car->is_primary)
                                    <span class="badge bg-primary">الافتراضية</span>
                                @endif
                            </div>
                            @if($car->year)<p class="mb-1"><strong>السنة:</strong> {{ $car->year }}</p>@endif
                            @if($car->plate_number)<p class="mb-1"><strong>اللوحة:</strong> {{ $car->plate_number }}</p>@endif
                            @if($car->color)<p class="mb-1"><strong>اللون:</strong> {{ $car->color }}</p>@endif
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('garage.maintenance', $car) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-journal-text"></i> سجل الصيانة</a>
                                @if(!$car->is_primary)
                                    <form action="{{ route('garage.primary', $car) }}" method="POST"><@csrf<button class="btn btn-sm btn-outline-secondary">تعيين كافتراضية</button></form>
                                @endif
                                <form action="{{ route('garage.destroy', $car) }}" method="POST" class="ms-auto">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف السيارة؟')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="modal fade" id="addCarModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form action="{{ route('garage.store') }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title">إضافة سيارة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">الماركة</label>
                    <select name="car_brand_id" class="form-select" id="carBrandSelect" required>
                        <option value="">اختر الماركة</option>
                        @foreach($carBrands as $brand)<option value="{{ $brand->id }}" data-models='@json($brand->models)'>{{ $brand->name }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">الموديل</label>
                    <select name="car_model_id" class="form-select" id="carModelSelect"><option value="">اختر الموديل</option></select>
                </div>
                <div class="row"><div class="col-6 mb-3"><label class="form-label">السنة</label>
                    <select name="year" class="form-select"><option value="">اختر</option>@for($y=date('Y')+1;$y>=1990;$y--)<option>{{ $y }}</option>@endfor</select>
                </div>
                <div class="col-6 mb-3"><label class="form-label">اللون</label><input type="text" name="color" class="form-control"></div></div>
                <div class="mb-3"><label class="form-label">رقم اللوحة</label><input type="text" name="plate_number" class="form-control"></div>
                <div class="mb-3"><label class="form-label">اسم مختصر</label><input type="text" name="nickname" class="form-control" placeholder="مثال: سيارتي الرئيسية"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إضافة</button></div>
        </form>
    </div></div>
</div>
<script>
document.getElementById('carBrandSelect').addEventListener('change', function() {
    const select = document.getElementById('carModelSelect');
    select.innerHTML = '<option value="">اختر الموديل</option>';
    if (this.value) {
        JSON.parse(this.selectedOptions[0].dataset.models).forEach(m => {
            select.innerHTML += `<option value="${m.id}">${m.name}</option>`;
        });
    }
});
</script>
@endsection
