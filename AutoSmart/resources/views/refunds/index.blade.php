@extends('layouts.app')
@section('title', 'طلبات الاسترداد')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-arrow-counterclockwise me-2"></i>طلبات الاسترداد</h1>

    @if($refunds->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-arrow-counterclockwise display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات استرداد</h4>
            <p class="text-muted">يمكنك طلب استرداد من صفحة تفاصيل الطلب</p>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>رقم الطلب</th><th>المبلغ</th><th>طريقة الاسترداد</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($refunds as $refund)
                            <tr>
                                <td><a href="{{ route('orders.show', $refund->order) }}">#{{ $refund->order->order_number }}</a></td>
                                <td>{{ number_format($refund->amount, 2) }} ر.س</td>
                                <td>{{ $refund->refund_type === 'wallet' ? 'المحفظة' : 'تحويل بنكي' }}</td>
                                <td><span class="badge bg-{{ $refund->status === 'processed' ? 'success' : ($refund->status === 'approved' ? 'info' : ($refund->status === 'rejected' ? 'danger' : 'warning')) }}">{{ $refund->status_label }}</span></td>
                                <td>{{ $refund->created_at->format('Y/m/d') }}</td>
                                <td><a href="{{ route('refunds.show', $refund) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>
        </div>
        <div class="mt-4">{{ $refunds->links() }}</div>
    @endif
</div>
@endsection
