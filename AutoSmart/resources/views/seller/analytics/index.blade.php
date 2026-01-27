@extends('layouts.seller')
@section('title', 'التحليلات')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">التحليلات والإحصائيات</h1>
    <form action="" method="GET"><select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="7" {{ $period == 7 ? 'selected' : '' }}>آخر 7 أيام</option>
        <option value="30" {{ $period == 30 ? 'selected' : '' }}>آخر 30 يوم</option>
        <option value="90" {{ $period == 90 ? 'selected' : '' }}>آخر 90 يوم</option>
    </select></form>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-primary mb-0">{{ number_format($summary['total_views']) }}</h3><small class="text-muted">إجمالي المشاهدات</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-info mb-0">{{ number_format($summary['unique_visitors']) }}</h3><small class="text-muted">زوار فريدين</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-success mb-0">{{ number_format($summary['total_orders']) }}</h3><small class="text-muted">الطلبات</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-warning mb-0">{{ number_format($summary['total_revenue'], 2) }}</h3><small class="text-muted">الإيرادات (ر.س)</small></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">الإيرادات اليومية</h5></div>
            <div class="card-body">
                @if($revenueByDay->isEmpty())<div class="text-center py-5 text-muted">لا توجد بيانات</div>
                @else<div class="table-responsive"><table class="table"><thead><tr><th>التاريخ</th><th>الإيرادات</th></tr></thead><tbody>@foreach($revenueByDay as $day)<tr><td>{{ $day->date }}</td><td>{{ number_format($day->total, 2) }} ر.س</td></tr>@endforeach</tbody></table></div>@endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">حالات الطلبات</h5></div>
            <div class="card-body">@foreach($ordersByStatus as $status => $count)<div class="d-flex justify-content-between mb-2"><span>{{ $status }}</span><span class="badge bg-secondary">{{ $count }}</span></div>@endforeach</div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="mb-0">الأكثر مبيعاً</h5></div>
            <div class="card-body p-0"><ul class="list-group list-group-flush">@foreach($topSelling->take(5) as $p)<li class="list-group-item d-flex justify-content-between"><span>{{ Str::limit($p->name, 25) }}</span><span class="badge bg-primary">{{ $p->sales_count }}</span></li>@endforeach</ul></div>
        </div>
    </div>
</div>
@endsection
