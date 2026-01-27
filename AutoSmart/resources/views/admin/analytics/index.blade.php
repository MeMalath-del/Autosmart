@extends('layouts.admin')
@section('title', 'التحليلات')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">التحليلات والإحصائيات</h1>
    <form action="" method="GET"><select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="7" {{ $period == 7 ? 'selected' : '' }}>آخر 7 أيام</option>
        <option value="30" {{ $period == 30 ? 'selected' : '' }}>آخر 30 يوم</option>
        <option value="90" {{ $period == 90 ? 'selected' : '' }}>آخر 90 يوم</option>
        <option value="365" {{ $period == 365 ? 'selected' : '' }}>سنة كاملة</option>
    </select></form>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body"><h5 class="mb-0">{{ number_format($stats['total_revenue'], 2) }} ر.س</h5><small>إجمالي الإيرادات</small></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body"><h5 class="mb-0">{{ number_format($stats['total_orders']) }}</h5><small>إجمالي الطلبات</small></div></div></div>
    <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body"><h5 class="mb-0">{{ number_format($stats['new_users']) }}</h5><small>مستخدمين جدد</small></div></div></div>
    <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body"><h5 class="mb-0">{{ number_format($stats['avg_order_value'] ?? 0, 2) }} ر.س</h5><small>متوسط قيمة الطلب</small></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">الإيرادات والطلبات</h5></div>
            <div class="card-body">
                @if($revenueByDay->isEmpty())<div class="text-center py-5 text-muted">لا توجد بيانات</div>
                @else<div class="table-responsive"><table class="table table-sm"><thead><tr><th>التاريخ</th><th>الطلبات</th><th>الإيرادات</th></tr></thead><tbody>
                    @foreach($revenueByDay as $day)<tr><td>{{ $day->date }}</td><td>{{ $day->orders }}</td><td>{{ number_format($day->revenue, 2) }} ر.س</td></tr>@endforeach
                </tbody></table></div>@endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">حالات الطلبات</h5></div>
            <div class="card-body">@foreach($ordersByStatus as $status => $count)<div class="d-flex justify-content-between mb-2"><span>{{ $status }}</span><span class="badge bg-secondary">{{ $count }}</span></div>@endforeach</div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="mb-0">البحث الشائع</h5></div>
            <div class="card-body p-0"><ul class="list-group list-group-flush">@foreach($popularSearches as $query => $count)<li class="list-group-item d-flex justify-content-between">{{ $query }}<span class="badge bg-primary">{{ $count }}</span></li>@endforeach</ul></div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">الأكثر مبيعاً</h5></div>
            <div class="card-body p-0"><table class="table table-sm mb-0"><tbody>@foreach($topProducts as $p)<tr><td>{{ Str::limit($p->name, 40) }}</td><td class="text-end"><span class="badge bg-success">{{ $p->sold ?? 0 }}</span></td></tr>@endforeach</tbody></table></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">المستخدمين الجدد</h5></div>
            <div class="card-body">
                @if($newUsersByDay->isEmpty())<div class="text-center py-3 text-muted">لا توجد بيانات</div>
                @else<div class="table-responsive"><table class="table table-sm"><thead><tr><th>التاريخ</th><th>العدد</th></tr></thead><tbody>@foreach($newUsersByDay->take(10) as $day)<tr><td>{{ $day->date }}</td><td>{{ $day->count }}</td></tr>@endforeach</tbody></table></div>@endif
            </div>
        </div>
    </div>
</div>
@endsection
