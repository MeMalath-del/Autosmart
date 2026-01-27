@extends('layouts.admin')
@section('title', 'سجل التدقيق')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-journal-text me-2"></i>سجل التدقيق</h1>

<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-3">
                <select name="event" class="form-select">
                    <option value="">كل الأحداث</option>
                    @foreach($events as $event)<option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>{{ $event }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="text" name="model" class="form-control" placeholder="النموذج" value="{{ request('model') }}"></div>
            <div class="col-md-3"><input type="number" name="user_id" class="form-control" placeholder="معرف المستخدم" value="{{ request('user_id') }}"></div>
            <div class="col-md-3"><button class="btn btn-primary w-100">بحث</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0">
            <thead><tr><th>الوقت</th><th>المستخدم</th><th>الحدث</th><th>النموذج</th><th>IP</th><th></th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><small>{{ $log->created_at->format('Y/m/d H:i:s') }}</small></td>
                        <td>{{ $log->user?->name ?? 'نظام' }}</td>
                        <td><span class="badge bg-{{ $log->event === 'created' ? 'success' : ($log->event === 'deleted' ? 'danger' : 'info') }}">{{ $log->event }}</span></td>
                        <td><small>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</small></td>
                        <td><small>{{ $log->ip_address }}</small></td>
                        <td><a href="{{ route('admin.audit.show', $log) }}" class="btn btn-sm btn-outline-primary">تفاصيل</a></td>
                    </tr>
                @empty<tr><td colspan="6" class="text-center py-4">لا توجد سجلات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
