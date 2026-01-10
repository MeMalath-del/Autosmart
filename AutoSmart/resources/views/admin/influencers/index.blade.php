@extends('layouts.admin')
@section('title', 'إدارة المؤثرين')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-stars me-2"></i>إدارة المؤثرين</h1>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['total'] }}</h3><small>إجمالي المؤثرين</small></div></div></div>
    <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['pending'] }}</h3><small>بانتظار المراجعة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['approved'] }}</h3><small>معتمدين</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-success mb-0">{{ number_format($stats['total_earnings'], 2) }}</h3><small class="text-muted">إجمالي العمولات</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>المؤثر</th><th>الرمز</th><th>المبيعات</th><th>العمولات</th><th>النسبة</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($influencers as $influencer)
                    <tr>
                        <td>
                            {{ $influencer->user->name }}
                            @if($influencer->is_verified)<i class="bi bi-patch-check-fill text-primary ms-1"></i>@endif
                        </td>
                        <td><code>{{ $influencer->code }}</code></td>
                        <td>{{ $influencer->total_sales }}</td>
                        <td>{{ number_format($influencer->total_earnings, 2) }} ر.س</td>
                        <td>{{ $influencer->commission_rate }}%</td>
                        <td><span class="badge bg-{{ $influencer->status === 'approved' ? 'success' : ($influencer->status === 'pending' ? 'warning' : 'danger') }}">{{ $influencer->status }}</span></td>
                        <td>
                            <a href="{{ route('admin.influencers.show', $influencer) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            @if($influencer->status === 'pending')
                                <form action="{{ route('admin.influencers.approve', $influencer) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">اعتماد</button></form>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا يوجد مؤثرين</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $influencers->links() }}</div>
@endsection
