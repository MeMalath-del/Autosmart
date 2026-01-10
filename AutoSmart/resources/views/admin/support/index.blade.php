@extends('layouts.admin')
@section('title', 'تذاكر الدعم')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-headset me-2"></i>تذاكر الدعم</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['open'] }}</h3><small>تذاكر مفتوحة</small></div></div></div>
    <div class="col-md-4"><div class="card bg-danger text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['urgent'] }}</h3><small>عاجلة</small></div></div></div>
    <div class="col-md-4"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['unassigned'] }}</h3><small>غير معينة</small></div></div></div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">التذاكر</h5>
        <form class="d-flex gap-2"><select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">كل الحالات</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوحة</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>جاري المعالجة</option>
            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>محلولة</option>
        </select></form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>رقم التذكرة</th><th>العميل</th><th>الموضوع</th><th>الفئة</th><th>الأولوية</th><th>الحالة</th><th>المعين</th><th></th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td><code>{{ $ticket->ticket_number }}</code></td>
                        <td>{{ $ticket->user->name }}</td>
                        <td>{{ Str::limit($ticket->subject, 30) }}</td>
                        <td>{{ $ticket->category }}</td>
                        <td><span class="badge bg-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'secondary') }}">{{ $ticket->priority_label }}</span></td>
                        <td><span class="badge bg-{{ $ticket->status === 'resolved' ? 'success' : 'info' }}">{{ $ticket->status_label }}</span></td>
                        <td>{{ $ticket->assignee?->name ?? '-' }}</td>
                        <td><a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                    </tr>
                @empty<tr><td colspan="8" class="text-center py-4">لا توجد تذاكر</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $tickets->links() }}</div>
@endsection
