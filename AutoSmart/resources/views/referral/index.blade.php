@extends('layouts.app')
@section('title', 'برنامج الإحالة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-people me-2"></i>برنامج الإحالة</h1>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-primary">
                <div class="card-body text-center py-4">
                    <h6 class="mb-3">كود الإحالة الخاص بك</h6>
                    <div class="bg-light p-3 rounded mb-3">
                        <h3 class="text-primary mb-0 font-monospace">{{ $user->referral_code }}</h3>
                    </div>
                    <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText('{{ $user->referral_code }}'); alert('تم النسخ!');">
                        <i class="bi bi-clipboard me-1"></i> نسخ الكود
                    </button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body text-center">
                    <h6 class="mb-3">شارك رابط الإحالة</h6>
                    <input type="text" class="form-control text-center mb-3" value="{{ url('/register?ref=' . $user->referral_code) }}" readonly>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="https://wa.me/?text={{ urlencode('انضم لـ AutoSmart واحصل على 10 ر.س! ' . url('/register?ref=' . $user->referral_code)) }}" class="btn btn-success" target="_blank"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode('انضم لـ AutoSmart واحصل على 10 ر.س! ' . url('/register?ref=' . $user->referral_code)) }}" class="btn btn-info" target="_blank"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h6 class="mb-3">كيف يعمل؟</h6>
                    <ol class="mb-0">
                        <li class="mb-2">شارك كودك مع أصدقائك</li>
                        <li class="mb-2">عند تسجيلهم وإتمام أول طلب</li>
                        <li class="mb-2">تحصل على <strong>20 ر.س</strong></li>
                        <li>صديقك يحصل على <strong>10 ر.س</strong></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="card text-center"><div class="card-body"><h3 class="text-primary mb-0">{{ $stats['total'] }}</h3><small class="text-muted">إجمالي الإحالات</small></div></div></div>
                <div class="col-md-4"><div class="card text-center"><div class="card-body"><h3 class="text-success mb-0">{{ $stats['qualified'] }}</h3><small class="text-muted">إحالات مؤهلة</small></div></div></div>
                <div class="col-md-4"><div class="card text-center"><div class="card-body"><h3 class="text-warning mb-0">{{ number_format($stats['earnings'], 2) }} ر.س</h3><small class="text-muted">إجمالي الأرباح</small></div></div></div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">الأصدقاء المُحالين</h5></div>
                <div class="card-body p-0">
                    @if($referrals->isEmpty())
                        <div class="text-center py-5"><i class="bi bi-people display-4 text-muted"></i><p class="text-muted mt-2">لم تقم بإحالة أي شخص بعد</p></div>
                    @else
                        <div class="table-responsive"><table class="table table-hover mb-0">
                            <thead><tr><th>الاسم</th><th>تاريخ التسجيل</th><th>الحالة</th><th>المكافأة</th></tr></thead>
                            <tbody>
                                @foreach($referrals as $r)
                                    <tr>
                                        <td>{{ $r->referred->name }}</td>
                                        <td>{{ $r->created_at->format('Y/m/d') }}</td>
                                        <td><span class="badge bg-{{ $r->status === 'rewarded' ? 'success' : ($r->status === 'qualified' ? 'info' : 'warning') }}">{{ $r->status === 'rewarded' ? 'تم المكافأة' : ($r->status === 'qualified' ? 'مؤهل' : 'في الانتظار') }}</span></td>
                                        <td>{{ $r->status === 'rewarded' ? number_format($r->referrer_reward, 2) . ' ر.س' : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
