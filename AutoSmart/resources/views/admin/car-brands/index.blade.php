@extends('layouts.admin')

@section('title', 'ماركات السيارات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="{{ route('admin.car-brands.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>إضافة ماركة
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الماركة</th>
                        <th>الدولة</th>
                        <th>الموديلات</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" class="me-3" 
                                             style="width: 45px; height: 45px; object-fit: contain;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px;">
                                            <i class="bi bi-car-front text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $brand->name }}</div>
                                        @if($brand->name_ar)
                                            <small class="text-muted">{{ $brand->name_ar }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $brand->country ?? '-' }}</td>
                            <td>{{ $brand->models_count }}</td>
                            <td>
                                @if($brand->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.car-brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.car-brands.destroy', $brand) }}" method="POST"
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
                            <td colspan="5" class="text-center py-5 text-muted">لا توجد ماركات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $brands->links() }}
</div>
@endsection
