@extends('layouts.app')
@section('title', 'المصادقة الثنائية')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h1 class="h3 mb-4"><i class="bi bi-shield-lock me-2"></i>المصادقة الثنائية</h1>
            
            <div class="card">
                <div class="card-body">
                    @if($enabled)
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                                <i class="bi bi-shield-check text-success fs-1"></i>
                            </div>
                            <h5 class="mt-3">المصادقة الثنائية مفعّلة</h5>
                            <p class="text-muted">حسابك محمي برمز تحقق إضافي</p>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="mb-2">أكواد الاسترداد</h6>
                            <p class="small mb-0">احتفظ بهذه الأكواد في مكان آمن. يمكنك استخدامها إذا فقدت الوصول لجهازك.</p>
                        </div>

                        <form action="{{ route('two-factor.disable') }}" method="POST">@csrf
                            <div class="mb-3">
                                <label class="form-label">كلمة المرور</label>
                                <input type="password" name="password" class="form-control" required placeholder="أدخل كلمة المرور للتأكيد">
                            </div>
                            <button type="submit" class="btn btn-danger w-100">إلغاء المصادقة الثنائية</button>
                        </form>
                    @else
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                                <i class="bi bi-shield-exclamation text-warning fs-1"></i>
                            </div>
                            <h5 class="mt-3">المصادقة الثنائية معطّلة</h5>
                            <p class="text-muted">فعّل المصادقة الثنائية لحماية إضافية</p>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6><i class="bi bi-info-circle me-1"></i> لماذا المصادقة الثنائية؟</h6>
                                <ul class="mb-0 small">
                                    <li>حماية إضافية لحسابك</li>
                                    <li>منع الوصول غير المصرح به</li>
                                    <li>إشعار فوري بمحاولات الدخول</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('two-factor.enable') }}" method="POST">@csrf
                            <div class="mb-3">
                                <label class="form-label">طريقة التحقق</label>
                                <div class="form-check">
                                    <input type="radio" name="method" value="sms" class="form-check-input" id="methodSms" checked>
                                    <label class="form-check-label" for="methodSms">رسالة SMS</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="method" value="email" class="form-check-input" id="methodEmail">
                                    <label class="form-check-label" for="methodEmail">البريد الإلكتروني</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">تفعيل المصادقة الثنائية</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
