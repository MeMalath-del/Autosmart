@extends('layouts.admin')
@section('title', 'ورش الصيانة')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">ورش الصيانة</h1>
    <form action="" method="GET"><select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">كل الحالات</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
    </select></form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>الورشة</th><th>المالك</th><th>المدينة</th><th>الحجوزات</th><th>التقييم</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($workshops as $workshop)
                    <tr>
                        <td>
                            {{ $workshop->name }}
                            @if($workshop->is_verified)<i class="bi bi-patch-check-fill text-primary"></i>@endif
                            @if($workshop->is_featured)<i class="bi bi-star-fill text-warning"></i>@endif
                        </td>
                        <td>{{ $workshop->user->name }}</td>
                        <td>{{ $workshop->city }}</td>
                        <td>{{ $workshop->bookings_count }}</td>
                        <td><span class="text-warning">{{ $workshop->rating }}</span> ({{ $workshop->reviews_count }})</td>
                        <td><span class="badge bg-{{ $workshop->status === 'approved' ? 'success' : ($workshop->status === 'pending' ? 'warning' : 'danger') }}">{{ $workshop->status }}</span></td>
                        <td>
                            @if($workshop->status === 'pending')
                                <form action="{{ route('admin.workshops.approve', $workshop) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">موافقة</button></form>
                            @endif
                            <form action="{{ route('admin.workshops.toggle-featured', $workshop) }}" method="POST" class="d-inline">@csrf
                                <button class="btn btn-sm btn-outline-warning" title="تمييز"><i class="bi bi-star{{ $workshop->is_featured ? '-fill' : '' }}"></i></button>
                            </form>
                            <form action="{{ route('admin.workshops.toggle-verified', $workshop) }}" method="POST" class="d-inline">@csrf
                                <button class="btn btn-sm btn-outline-primary" title="توثيق"><i class="bi bi-patch-check{{ $workshop->is_verified ? '-fill' : '' }}"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد ورش</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
</div>
<div class="mt-4">{{ $workshops->links() }}</div>
@endsection
