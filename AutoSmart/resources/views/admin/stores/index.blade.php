@extends('layouts.admin')

@section('title', 'إدارة المتاجر')

@section('content')
<div class="card">
    <div class="card-header">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو البريد..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100">بحث</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المتجر</th>
                        <th>صاحب المتجر</th>
                        <th>المدينة</th>
                        <th>المنتجات</th>
                        <th>التقييم</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($store->logo)
                                        <img src="{{ asset('storage/' . $store->logo) }}" class="rounded-circle me-2" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px;">
                                            {{ mb_substr($store->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">
                                            {{ $store->name }}
                                            @if($store->is_verified)<i class="bi bi-patch-check-fill text-primary"></i>@endif
                                            @if($store->is_featured)<i class="bi bi-star-fill text-warning"></i>@endif
                                        </div>
                                        <small class="text-muted">{{ $store->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $store->user->name }}</td>
                            <td>{{ $store->city }}</td>
                            <td>{{ $store->products_count }}</td>
                            <td>
                                <span class="text-warning"><i class="bi bi-star-fill"></i></span>
                                {{ number_format($store->rating, 1) }}
                            </td>
                            <td>
                                @switch($store->status)
                                    @case('pending')
                                        <span class="badge bg-warning">قيد المراجعة</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-success">معتمد</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">مرفوض</span>
                                        @break
                                    @case('suspended')
                                        <span class="badge bg-secondary">معلق</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $store->created_at->format('Y/m/d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.stores.show', $store) }}">
                                            <i class="bi bi-eye me-2"></i>عرض
                                        </a></li>
                                        @if($store->status == 'pending')
                                            <li>
                                                <form action="{{ route('admin.stores.approve', $store) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="bi bi-check-circle me-2"></i>قبول
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.stores.reject', $store) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-x-circle me-2"></i>رفض
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($store->status == 'approved')
                                            <li>
                                                <form action="{{ route('admin.stores.suspend', $store) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-warning">
                                                        <i class="bi bi-pause-circle me-2"></i>تعليق
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($store->status == 'suspended')
                                            <li>
                                                <form action="{{ route('admin.stores.activate', $store) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="bi bi-play-circle me-2"></i>تفعيل
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.stores.toggle-verified', $store) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-patch-check me-2"></i>
                                                    {{ $store->is_verified ? 'إلغاء التوثيق' : 'توثيق' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.stores.toggle-featured', $store) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-star me-2"></i>
                                                    {{ $store->is_featured ? 'إلغاء التميز' : 'تمييز' }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">لا توجد متاجر</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $stores->links() }}
</div>
@endsection
