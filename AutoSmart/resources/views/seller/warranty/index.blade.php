@extends('layouts.seller')

@section('title', 'طلبات الضمان')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">طلبات الضمان</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h3 class="text-warning mb-0">{{ $stats['pending'] }}</h3>
                <small>قيد الانتظار</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body text-center">
                <h3 class="text-info mb-0">{{ $stats['under_review'] }}</h3>
                <small>تحت المراجعة</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h3 class="text-success mb-0">{{ $stats['approved'] }}</h3>
                <small>تمت الموافقة</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="" method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">جميع الحالات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>تحت المراجعة</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        @if($claims->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-shield-check display-4 text-muted"></i>
                <p class="text-muted mt-2">لا توجد طلبات ضمان</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>رقم المطالبة</th>
                            <th>العميل</th>
                            <th>المنتج</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($claims as $claim)
                            <tr>
                                <td><code>{{ $claim->claim_number }}</code></td>
                                <td>{{ $claim->user->name }}</td>
                                <td>{{ Str::limit($claim->product->name, 30) }}</td>
                                <td>{{ $claim->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <span class="badge bg-{{ $claim->status_color }}">
                                        {{ $claim->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('seller.warranty.show', $claim) }}" class="btn btn-sm btn-outline-primary">
                                        عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="mt-4">
    {{ $claims->links() }}
</div>
@endsection
