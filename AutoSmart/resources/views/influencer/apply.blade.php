@extends('layouts.app')
@section('title', 'انضم لبرنامج المؤثرين')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center mb-5">
            <i class="bi bi-stars display-1 text-primary"></i>
            <h1 class="h2 mt-3">انضم لبرنامج المؤثرين</h1>
            <p class="text-muted">اربح عمولة على كل عملية شراء عبر رابطك</p>
        </div>
    </div>

    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-md-3 text-center"><i class="bi bi-link-45deg display-4 text-primary"></i><h5 class="mt-2">رابط خاص</h5><p class="text-muted small">احصل على رابط ترويجي فريد</p></div>
        <div class="col-md-3 text-center"><i class="bi bi-cash-coin display-4 text-success"></i><h5 class="mt-2">عمولة مجزية</h5><p class="text-muted small">حتى 10% من كل عملية بيع</p></div>
        <div class="col-md-3 text-center"><i class="bi bi-graph-up display-4 text-info"></i><h5 class="mt-2">تقارير مفصلة</h5><p class="text-muted small">تتبع مبيعاتك وأرباحك</p></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">تقديم طلب الانضمام</h5></div>
                <div class="card-body">
                    <form action="{{ route('influencer.apply') }}" method="POST">@csrf
                        <div class="mb-3">
                            <label class="form-label">نبذة عنك</label>
                            <textarea name="bio" class="form-control" rows="3" required placeholder="اخبرنا عن نفسك ومحتواك..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">قناة يوتيوب</label>
                            <input type="url" name="youtube" class="form-control" placeholder="https://youtube.com/@channel">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">انستقرام</label>
                            <div class="input-group"><span class="input-group-text">@</span><input type="text" name="instagram" class="form-control"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تويتر</label>
                            <div class="input-group"><span class="input-group-text">@</span><input type="text" name="twitter" class="form-control"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تيك توك</label>
                            <div class="input-group"><span class="input-group-text">@</span><input type="text" name="tiktok" class="form-control"></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">تقديم الطلب</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
