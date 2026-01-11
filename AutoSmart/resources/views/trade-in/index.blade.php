@extends('layouts.app')
@section('title', 'طلبات الاستبدال')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-arrow-repeat me-2"></i>برنامج استبدال القطع القديمة</h1>

    <div class="alert alert-info mb-4">
        <i class="bi bi-lightbulb me-2"></i>قم باستبدال قطعك القديمة واحصل على خصم على شراء القطع الجديدة!
    </div>

    @if($requests->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-arrow-repeat display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات استبدال</h4>
            <p class="text-muted">تصفح المنتجات واختر "استبدال قطعة قديمة" للحصول على خصم</p>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>المنتج المطلوب</th><th>القطعة القديمة</th><th>الخصم المقدر</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->product->name }}</td>
                                <td>{{ $request->old_part_name }} {{ $request->old_part_brand ? '(' . $request->old_part_brand . ')' : '' }}</td>
                                <td class="text-success">{{ $request->offered_discount ? number_format($request->offered_discount, 2) . ' ر.س' : 'قيد التقييم' }}</td>
                                <td><span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'info') }}">{{ $request->status }}</span></td>
                                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                                <td><a href="{{ route('trade-in.show', $request) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
