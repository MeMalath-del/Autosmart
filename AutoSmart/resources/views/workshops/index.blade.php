@extends('layouts.app')
@section('title', 'ورش الصيانة')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-wrench me-2"></i>ورش الصيانة</h1>
        @auth<a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> طلب صيانة</a>@endauth
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}"></div>
                <div class="col-md-3"><select name="city" class="form-select"><option value="">كل المدن</option>@foreach($cities as $city)<option {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>@endforeach</select></div>
                <div class="col-md-3"><button class="btn btn-primary w-100">بحث</button></div>
            </form>
        </div>
    </div>

    @if($workshops->isEmpty())
        <div class="text-center py-5"><i class="bi bi-wrench display-1 text-muted"></i><h4 class="mt-3">لا توجد ورش متاحة</h4></div>
    @else
        <div class="row g-4">
            @foreach($workshops as $workshop)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($workshop->logo)<img src="{{ asset('storage/'.$workshop->logo) }}" class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                                @else<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:60px;height:60px;"><i class="bi bi-wrench fs-4"></i></div>@endif
                                <div>
                                    <h5 class="mb-0">{{ $workshop->name }}@if($workshop->is_verified)<i class="bi bi-patch-check-fill text-primary ms-1"></i>@endif</h5>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $workshop->city }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">@for($i=1;$i<=5;$i++)<i class="bi {{ $workshop->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor</div>
                                <small class="text-muted">({{ $workshop->reviews_count }} تقييم)</small>
                            </div>
                            @if($workshop->specialties)<div class="mb-3">@foreach(array_slice($workshop->specialties, 0, 3) as $s)<span class="badge bg-light text-dark me-1">{{ $s }}</span>@endforeach</div>@endif
                            <p class="text-muted small mb-3">{{ Str::limit($workshop->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $workshop->services_count }} خدمة</small>
                                <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-sm btn-outline-primary">عرض التفاصيل</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $workshops->links() }}</div>
    @endif
</div>
@endsection
