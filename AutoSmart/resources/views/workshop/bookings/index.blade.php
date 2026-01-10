@extends('layouts.app')
@section('title', 'حجوزات الورشة')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">حجوزات الورشة</h1>
        <form action="" method="GET"><select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">كل الحالات</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>جاري التنفيذ</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
        </select></form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead><tr><th>رقم الحجز</th><th>العميل</th><th>التاريخ</th><th>الوقت</th><th>التكلفة المقدرة</th><th>الحالة</th><th></th></tr></thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_number }}</td>
                            <td>{{ $booking->user->name }}<br><small class="text-muted">{{ $booking->user->phone }}</small></td>
                            <td>{{ $booking->booking_date->format('Y/m/d') }}</td>
                            <td>{{ $booking->booking_time }}</td>
                            <td>{{ $booking->estimated_cost ? number_format($booking->estimated_cost, 2) . ' ر.س' : '-' }}</td>
                            <td><span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'in_progress' ? 'primary' : ($booking->status === 'confirmed' ? 'info' : 'warning')) }}">{{ $booking->status_label }}</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                    <ul class="dropdown-menu">
                                        @if($booking->status === 'pending')
                                            <li><form action="{{ route('workshop.bookings.update', $booking) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="confirmed"><button class="dropdown-item">تأكيد</button></form></li>
                                        @endif
                                        @if($booking->status === 'confirmed')
                                            <li><form action="{{ route('workshop.bookings.update', $booking) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="in_progress"><button class="dropdown-item">بدء العمل</button></form></li>
                                        @endif
                                        @if($booking->status === 'in_progress')
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#completeModal{{ $booking->id }}">إنهاء</button></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        
                        @if($booking->status === 'in_progress')
                        <div class="modal fade" id="completeModal{{ $booking->id }}" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content">
                                <form action="{{ route('workshop.bookings.update', $booking) }}" method="POST">@csrf @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <div class="modal-header"><h5 class="modal-title">إنهاء الحجز</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <label class="form-label">التكلفة النهائية</label>
                                        <input type="number" name="final_cost" class="form-control" step="0.01" value="{{ $booking->estimated_cost }}">
                                    </div>
                                    <div class="modal-footer"><button type="submit" class="btn btn-success">إنهاء الحجز</button></div>
                                </form>
                            </div></div>
                        </div>
                        @endif
                    @empty<tr><td colspan="7" class="text-center py-4">لا توجد حجوزات</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
@endsection
