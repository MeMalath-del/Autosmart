@extends('layouts.admin')
@section('title', 'طلبات الاسترداد')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">طلبات الاسترداد</h1>
    <form action="" method="GET"><select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">كل الحالات</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>تم التنفيذ</option>
        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
    </select></form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>العميل</th><th>رقم الطلب</th><th>المبلغ</th><th>الطريقة</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
                @forelse($refunds as $refund)
                    <tr>
                        <td>{{ $refund->user->name }}</td>
                        <td>#{{ $refund->order->order_number }}</td>
                        <td>{{ number_format($refund->amount, 2) }} ر.س</td>
                        <td>{{ $refund->refund_type === 'wallet' ? 'المحفظة' : 'بنكي' }}</td>
                        <td><span class="badge bg-{{ $refund->status === 'processed' ? 'success' : ($refund->status === 'approved' ? 'info' : ($refund->status === 'rejected' ? 'danger' : 'warning')) }}">{{ $refund->status_label }}</span></td>
                        <td>{{ $refund->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('admin.refunds.show', $refund) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            @if($refund->status === 'pending')
                                <form action="{{ route('admin.refunds.approve', $refund) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success"><i class="bi bi-check"></i></button></form>
                            @endif
                            @if($refund->status === 'approved')
                                <form action="{{ route('admin.refunds.process', $refund) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-primary">تنفيذ</button></form>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد طلبات</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
</div>
<div class="mt-4">{{ $refunds->links() }}</div>
@endsection
