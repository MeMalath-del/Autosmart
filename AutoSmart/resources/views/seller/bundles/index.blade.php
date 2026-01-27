@extends('layouts.seller')
@section('title', 'الباقات المجمعة')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-box2-heart me-2"></i>الباقات المجمعة</h1>
    <a href="{{ route('seller.bundles.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> باقة جديدة</a>
</div>

@if($bundles->isEmpty())
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-box2-heart display-1 text-muted"></i>
        <h4 class="mt-3">لا توجد باقات</h4>
        <p class="text-muted">أنشئ باقات من منتجاتك وقدم خصومات للعملاء</p>
        <a href="{{ route('seller.bundles.create') }}" class="btn btn-primary">إنشاء باقة</a>
    </div></div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>الباقة</th><th>المنتجات</th><th>السعر العادي</th><th>سعر الباقة</th><th>التوفير</th><th>الحالة</th><th></th></tr></thead>
                <tbody>
                    @foreach($bundles as $bundle)
                        <tr>
                            <td>{{ $bundle->name }}</td>
                            <td>{{ $bundle->products->count() }} منتج</td>
                            <td class="text-muted"><s>{{ number_format($bundle->regular_price, 2) }} ر.س</s></td>
                            <td class="text-primary fw-bold">{{ number_format($bundle->bundle_price, 2) }} ر.س</td>
                            <td><span class="badge bg-success">{{ $bundle->savings_percentage }}%</span></td>
                            <td><span class="badge bg-{{ $bundle->is_active ? 'success' : 'secondary' }}">{{ $bundle->is_active ? 'نشط' : 'معطل' }}</span></td>
                            <td>
                                <a href="{{ route('seller.bundles.edit', $bundle) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                <form action="{{ route('seller.bundles.destroy', $bundle) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف الباقة؟')">حذف</button>
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
