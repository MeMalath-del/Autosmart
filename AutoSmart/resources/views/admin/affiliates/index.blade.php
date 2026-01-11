@extends('layouts.admin')
@section('title', 'الشركاء التسويقيين')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-share me-2"></i>الشركاء التسويقيين (Affiliates)</h1>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['total'] }}</h3><small>إجمالي الشركاء</small></div></div></div>
    <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['pending'] }}</h3><small>بانتظار المراجعة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['approved'] }}</h3><small>معتمدين</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-success mb-0">{{ number_format($stats['total_earnings'], 2) }}</h3><small class="text-muted">إجمالي العمولات</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>الشريك</th><th>الرمز</th><th>النقرات</th><th>التحويلات</th><th>الأرباح</th><th>العمولة</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($affiliates as $affiliate)
                    <tr>
                        <td>{{ $affiliate->user->name }}</td>
                        <td><code>{{ $affiliate->code }}</code></td>
                        <td>{{ $affiliate->total_clicks }}</td>
                        <td>{{ $affiliate->total_orders }} ({{ number_format($affiliate->conversion_rate, 1) }}%)</td>
                        <td>{{ number_format($affiliate->total_earnings, 2) }} ر.س</td>
                        <td>{{ $affiliate->commission_rate }}%</td>
                        <td><span class="badge bg-{{ $affiliate->status === 'approved' ? 'success' : ($affiliate->status === 'pending' ? 'warning' : 'danger') }}">{{ $affiliate->status }}</span></td>
                        <td>
                            <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            @if($affiliate->status === 'pending')
                                <form action="{{ route('admin.affiliates.approve', $affiliate) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">اعتماد</button></form>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="8" class="text-center py-4">لا يوجد شركاء</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $affiliates->links() }}</div>
@endsection
