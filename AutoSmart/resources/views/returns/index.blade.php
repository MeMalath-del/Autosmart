@extends('layouts.app')
@section('title', 'طلبات الإرجاع')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-arrow-counterclockwise me-2"></i>طلبات الإرجاع</h1>

    @if($returns->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-box-arrow-left display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات إرجاع</h4>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>رقم الطلب</th><th>الطلب الأصلي</th><th>النوع</th><th>السبب</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($returns as $return)
                            <tr>
                                <td><code>{{ $return->return_number }}</code></td>
                                <td><a href="{{ route('orders.show', $return->order) }}">{{ $return->order->order_number }}</a></td>
                                <td>{{ $return->type === 'return' ? 'إرجاع' : 'استبدال' }}</td>
                                <td>{{ $return->reason_label }}</td>
                                <td><span class="badge bg-{{ $return->status === 'completed' ? 'success' : ($return->status === 'rejected' ? 'danger' : 'info') }}">{{ $return->status }}</span></td>
                                <td>{{ $return->created_at->format('Y/m/d') }}</td>
                                <td><a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $returns->links() }}</div>
    @endif
</div>
@endsection
