@extends('layouts.app')
@section('title', 'حسابات الشركات')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="bi bi-building display-1 text-primary"></i>
                <h1 class="h2 mt-3">حسابات الشركات B2B</h1>
                <p class="text-muted">سجل كتاجر واحصل على مميزات حصرية</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4 text-center"><i class="bi bi-percent display-4 text-primary"></i><h5 class="mt-2">أسعار الجملة</h5><p class="text-muted small">خصومات كبيرة على الكميات</p></div>
                <div class="col-md-4 text-center"><i class="bi bi-credit-card display-4 text-primary"></i><h5 class="mt-2">الدفع الآجل</h5><p class="text-muted small">حد ائتماني للمشتريات</p></div>
                <div class="col-md-4 text-center"><i class="bi bi-headset display-4 text-primary"></i><h5 class="mt-2">دعم مخصص</h5><p class="text-muted small">مدير حساب خاص</p></div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">تسجيل حساب تجاري</h5></div>
                <div class="card-body">
                    <form action="{{ route('b2b.register') }}" method="POST">@csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم الشركة (إنجليزي)</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم الشركة (عربي)</label>
                                <input type="text" name="company_name_ar" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">نوع النشاط</label>
                                <select name="business_type" class="form-select" required>
                                    <option value="workshop">ورشة صيانة</option>
                                    <option value="dealer">وكيل سيارات</option>
                                    <option value="wholesaler">تاجر جملة</option>
                                    <option value="retailer">تاجر تجزئة</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرقم الضريبي</label>
                                <input type="text" name="tax_number" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">السجل التجاري</label>
                                <input type="text" name="commercial_register" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <textarea name="address" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم جهة الاتصال</label>
                                <input type="text" name="contact_person" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الجوال</label>
                                <input type="tel" name="contact_phone" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">تقديم الطلب</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
