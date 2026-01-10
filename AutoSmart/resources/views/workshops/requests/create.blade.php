@extends('layouts.app')
@section('title', 'طلب صيانة جديد')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-plus-circle me-2"></i>طلب صيانة جديد</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('maintenance.requests.store') }}" method="POST" enctype="multipart/form-data">@csrf
                        <div class="mb-3">
                            <label class="form-label">السيارة</label>
                            @if($cars->count())
                                <select name="user_car_id" class="form-select">
                                    <option value="">اختر سيارة مسجلة أو أدخل يدوياً</option>
                                    @foreach($cars as $car)<option value="{{ $car->id }}">{{ $car->display_name }}</option>@endforeach
                                </select>
                            @endif
                            <input type="text" name="car_info" class="form-control mt-2" placeholder="أو اكتب معلومات السيارة (مثال: تويوتا كامري 2020)">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف المشكلة</label>
                            <textarea name="issue_description" class="form-control" rows="5" required placeholder="اشرح المشكلة بالتفصيل..."></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">مستوى الأهمية</label>
                                <select name="urgency" class="form-select">
                                    <option value="low">عادي</option>
                                    <option value="medium" selected>متوسط</option>
                                    <option value="high">عاجل</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">التاريخ المفضل</label>
                                <input type="date" name="preferred_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الوقت المفضل</label>
                                <select name="preferred_time" class="form-select">
                                    <option value="">غير محدد</option>
                                    <option value="morning">صباحاً (8-12)</option>
                                    <option value="afternoon">ظهراً (12-4)</option>
                                    <option value="evening">مساءً (4-8)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المدينة</label>
                            <input type="text" name="city" class="form-control" required placeholder="مثال: الرياض">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">صور توضيحية (اختياري)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <div class="form-text">يمكنك رفع حتى 5 صور</div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">إرسال الطلب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6><i class="bi bi-info-circle me-1"></i> كيف يعمل؟</h6>
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">أرسل طلب الصيانة مع وصف المشكلة</li>
                        <li class="mb-2">ستتلقى عروض أسعار من ورش الصيانة</li>
                        <li class="mb-2">قارن العروض واختر الأنسب</li>
                        <li>احجز موعد الصيانة</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
