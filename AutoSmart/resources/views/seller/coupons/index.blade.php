@extends('layouts.seller')

@section('title', 'الكوبونات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">الكوبونات</h1>
    <a href="{{ route('seller.coupons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> كوبون جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($coupons->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-percent display-4 text-muted"></i>
                <p class="text-muted mt-2">لا توجد كوبونات</p>
                <a href="{{ route('seller.coupons.create') }}" class="btn btn-primary">إنشاء كوبون</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>الكود</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>القيمة</th>
                            <th>الاستخدام</th>
                            <th>الحالة</th>
                            <th>الصلاحية</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td><code class="fs-6">{{ $coupon->code }}</code></td>
                                <td>{{ $coupon->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $coupon->type_label }}</span>
                                </td>
                                <td>
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @elseif($coupon->type === 'fixed')
                                        {{ number_format($coupon->value) }} ر.س
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{ $coupon->usages_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->isValid())
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->expires_at)
                                        {{ $coupon->expires_at->format('Y/m/d') }}
                                    @else
                                        دائم
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('seller.coupons.edit', $coupon) }}" class="dropdown-item">
                                                    <i class="bi bi-pencil me-2"></i> تعديل
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('seller.coupons.destroy', $coupon) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" 
                                                            onclick="return confirm('هل تريد حذف هذا الكوبون؟')">
                                                        <i class="bi bi-trash me-2"></i> حذف
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
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
    {{ $coupons->links() }}
</div>
@endsection
