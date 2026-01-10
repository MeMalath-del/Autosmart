@extends('layouts.admin')
@section('title', 'حسابات الشركات')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-building me-2"></i>حسابات الشركات B2B</h1>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['total'] }}</h3><small>إجمالي الحسابات</small></div></div></div>
    <div class="col-md-4"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['pending'] }}</h3><small>بانتظار المراجعة</small></div></div></div>
    <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['approved'] }}</h3><small>معتمدة</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>الشركة</th><th>نوع النشاط</th><th>جهة الاتصال</th><th>حد الائتمان</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>
                            <strong>{{ $account->company_name }}</strong><br>
                            <small class="text-muted">{{ $account->user->email }}</small>
                        </td>
                        <td>{{ $account->business_type_label }}</td>
                        <td>{{ $account->contact_person }}<br><small>{{ $account->contact_phone }}</small></td>
                        <td>{{ number_format($account->credit_limit, 2) }} ر.س</td>
                        <td><span class="badge bg-{{ $account->status === 'approved' ? 'success' : ($account->status === 'pending' ? 'warning' : 'danger') }}">{{ $account->status }}</span></td>
                        <td>
                            <a href="{{ route('admin.b2b.show', $account) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            @if($account->status === 'pending')
                                <form action="{{ route('admin.b2b.approve', $account) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">اعتماد</button></form>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="6" class="text-center py-4">لا توجد حسابات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $accounts->links() }}</div>
@endsection
