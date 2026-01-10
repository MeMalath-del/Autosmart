@extends('layouts.seller')
@section('title', 'إدارة الموظفين')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-people me-2"></i>إدارة الموظفين</h1>
    <a href="{{ route('seller.staff.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> إضافة موظف</a>
</div>

@if($staff->isEmpty())
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-people display-1 text-muted"></i>
        <h4 class="mt-3">لا يوجد موظفين</h4>
        <p class="text-muted">أضف موظفين لمساعدتك في إدارة المتجر</p>
        <a href="{{ route('seller.staff.create') }}" class="btn btn-primary">إضافة موظف</a>
    </div></div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>الموظف</th><th>المنصب</th><th>الصلاحيات</th><th>الحالة</th><th></th></tr></thead>
                <tbody>
                    @foreach($staff as $member)
                        <tr>
                            <td>
                                <strong>{{ $member->user->name }}</strong><br>
                                <small class="text-muted">{{ $member->user->email }}</small>
                            </td>
                            <td>{{ $member->position ?? '-' }}</td>
                            <td>
                                @if($member->can_manage_products)<span class="badge bg-info me-1">المنتجات</span>@endif
                                @if($member->can_manage_orders)<span class="badge bg-success me-1">الطلبات</span>@endif
                                @if($member->can_manage_inventory)<span class="badge bg-warning me-1">المخزون</span>@endif
                                @if($member->can_view_reports)<span class="badge bg-secondary me-1">التقارير</span>@endif
                            </td>
                            <td><span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }}">{{ $member->is_active ? 'نشط' : 'معطل' }}</span></td>
                            <td>
                                <a href="{{ route('seller.staff.edit', $member) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                <form action="{{ route('seller.staff.destroy', $member) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
