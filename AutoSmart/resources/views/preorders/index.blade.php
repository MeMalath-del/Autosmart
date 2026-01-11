@extends('layouts.app')
@section('title', 'طلباتي المسبقة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-clock-history me-2"></i>طلباتي المسبقة</h1>

    @if($preorders->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-clock display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات مسبقة</h4>
            <p class="text-muted">يمكنك حجز المنتجات قبل توفرها من صفحة المنتج</p>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>المنتج</th><th>الكمية</th><th>العربون</th><th>التوفر المتوقع</th><th>الحالة</th><th></th></tr></thead>
                    <tbody>
                        @foreach($preorders as $preorder)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $preorder->product->primary_image_url }}" class="rounded me-2" style="width:50px;height:50px;object-fit:cover;">
                                        <span>{{ $preorder->product->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $preorder->quantity }}</td>
                                <td>
                                    {{ number_format($preorder->deposit_amount, 2) }} ر.س
                                    @if($preorder->deposit_paid)<span class="badge bg-success">مدفوع</span>@else<span class="badge bg-warning">غير مدفوع</span>@endif
                                </td>
                                <td>{{ $preorder->expected_date?->format('Y/m/d') ?? 'غير محدد' }}</td>
                                <td><span class="badge bg-{{ $preorder->status === 'ready' ? 'success' : ($preorder->status === 'cancelled' ? 'danger' : 'info') }}">{{ $preorder->status }}</span></td>
                                <td>
                                    <a href="{{ route('preorders.show', $preorder) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                    @if(in_array($preorder->status, ['pending', 'confirmed']))
                                        <form action="{{ route('preorders.cancel', $preorder) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-danger" onclick="return confirm('إلغاء الطلب؟')">إلغاء</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $preorders->links() }}</div>
    @endif
</div>
@endsection
