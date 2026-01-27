@extends('layouts.admin')
@section('title', 'حملات الإشعارات')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-bell me-2"></i>حملات الإشعارات</h1>
    <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> حملة جديدة</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>الحملة</th><th>العنوان</th><th>المستهدفين</th><th>التسليم</th><th>الفتح</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->name }}</td>
                        <td>{{ Str::limit($campaign->title, 30) }}</td>
                        <td>{{ $campaign->total_recipients }}</td>
                        <td>{{ $campaign->delivered_count }} ({{ number_format($campaign->total_recipients > 0 ? ($campaign->delivered_count / $campaign->total_recipients) * 100 : 0, 1) }}%)</td>
                        <td>{{ $campaign->opened_count }} ({{ number_format($campaign->open_rate, 1) }}%)</td>
                        <td><span class="badge bg-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'scheduled' ? 'info' : 'secondary') }}">{{ $campaign->status }}</span></td>
                        <td><a href="{{ route('admin.notifications.show', $campaign) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد حملات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
