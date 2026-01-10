@extends('layouts.admin')
@section('title', 'حملات البريد الإلكتروني')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">حملات البريد الإلكتروني</h1>
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> حملة جديدة</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>الاسم</th><th>الموضوع</th><th>الشريحة</th><th>الحالة</th><th>المرسل</th><th>معدل الفتح</th><th></th></tr></thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->name }}</td>
                        <td>{{ Str::limit($campaign->subject, 30) }}</td>
                        <td>{{ $campaign->segment }}</td>
                        <td><span class="badge bg-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'scheduled' ? 'info' : 'secondary') }}">{{ $campaign->status }}</span></td>
                        <td>{{ $campaign->sent_count }} / {{ $campaign->recipients_count }}</td>
                        <td>{{ $campaign->open_rate }}%</td>
                        <td>
                            @if($campaign->status !== 'sent')
                                <form action="{{ route('admin.campaigns.send', $campaign) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-sm btn-success" onclick="return confirm('إرسال الحملة؟')"><i class="bi bi-send"></i></button>
                                </form>
                            @endif
                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف الحملة؟')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد حملات</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
</div>
<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
