@extends('layouts.admin')
@section('title', 'التسعير الديناميكي')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-graph-up-arrow me-2"></i>التسعير الديناميكي</h1>
    <div>
        <form action="{{ route('admin.pricing.apply') }}" method="POST" class="d-inline">@csrf<button class="btn btn-success"><i class="bi bi-play-circle me-1"></i>تطبيق القواعد</button></form>
        <a href="{{ route('admin.pricing.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> قاعدة جديدة</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>القاعدة</th><th>النوع</th><th>التأثير</th><th>القيمة</th><th>الأولوية</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td>{{ $rule->name }}<br><small class="text-muted">{{ $rule->product?->name ?? $rule->category?->name ?? 'عام' }}</small></td>
                        <td><span class="badge bg-secondary">{{ $rule->type }}</span></td>
                        <td>{{ $rule->action === 'increase' ? 'زيادة' : ($rule->action === 'decrease' ? 'تخفيض' : 'تحديد') }}</td>
                        <td>{{ $rule->value }}{{ $rule->value_type === 'percentage' ? '%' : ' ر.س' }}</td>
                        <td>{{ $rule->priority }}</td>
                        <td><span class="badge bg-{{ $rule->is_active ? 'success' : 'secondary' }}">{{ $rule->is_active ? 'نشط' : 'معطل' }}</span></td>
                        <td>
                            <form action="{{ route('admin.pricing.destroy', $rule) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف القاعدة؟')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد قواعد تسعير</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $rules->links() }}</div>
@endsection
