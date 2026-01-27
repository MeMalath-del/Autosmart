@extends('layouts.app')
@section('title', 'سجل الصيانة - ' . $userCar->display_name)
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('garage.index') }}" class="text-decoration-none"><i class="bi bi-arrow-right me-1"></i>العودة لسياراتي</a>
            <h1 class="h3 mb-0 mt-2">سجل صيانة {{ $userCar->display_name }}</h1>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLogModal"><i class="bi bi-plus-lg me-1"></i> إضافة سجل</button>
    </div>

    @if($logs->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-journal-text display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد سجلات صيانة</h4>
            <p class="text-muted">سجّل أعمال الصيانة لتتبع تاريخ سيارتك</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLogModal">إضافة سجل</button>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>التاريخ</th><th>النوع</th><th>العداد</th><th>الوصف</th><th>مقدم الخدمة</th><th>التكلفة</th></tr></thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->maintenance_date->format('Y/m/d') }}</td>
                                <td><span class="badge bg-secondary">{{ $log->type_label }}</span></td>
                                <td>{{ $log->mileage ? number_format($log->mileage) . ' كم' : '-' }}</td>
                                <td>{{ Str::limit($log->description, 40) }}</td>
                                <td>{{ $log->service_provider ?? '-' }}</td>
                                <td>{{ $log->cost ? number_format($log->cost, 2) . ' ر.س' : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    @endif
</div>

<div class="modal fade" id="addLogModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form action="{{ route('garage.maintenance.store', $userCar) }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title">إضافة سجل صيانة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">التاريخ</label><input type="date" name="maintenance_date" class="form-control" required value="{{ date('Y-m-d') }}"></div>
                    <div class="col-md-6"><label class="form-label">النوع</label>
                        <select name="type" class="form-select" required>
                            <option value="oil_change">تغيير زيت</option>
                            <option value="tire_rotation">تدوير إطارات</option>
                            <option value="brake_service">صيانة فرامل</option>
                            <option value="battery">بطارية</option>
                            <option value="filter">فلاتر</option>
                            <option value="general">صيانة عامة</option>
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">قراءة العداد (كم)</label><input type="number" name="mileage" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">التكلفة</label><input type="number" name="cost" class="form-control" step="0.01"></div>
                    <div class="col-12"><label class="form-label">مقدم الخدمة</label><input type="text" name="service_provider" class="form-control" placeholder="اسم الورشة أو المركز"></div>
                    <div class="col-12"><label class="form-label">ملاحظات</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">حفظ</button></div>
        </form>
    </div></div>
</div>
@endsection
