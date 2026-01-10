@extends('layouts.app')
@section('title', $workshop->name)
@section('content')
<div class="container py-4">
    @if($workshop->banner)<div class="rounded-3 mb-4" style="height:200px;background:url('{{ asset('storage/'.$workshop->banner) }}') center/cover;"></div>@endif
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if($workshop->logo)<img src="{{ asset('storage/'.$workshop->logo) }}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                        @else<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:80px;height:80px;"><i class="bi bi-wrench fs-3"></i></div>@endif
                        <div>
                            <h2 class="mb-1">{{ $workshop->name }}@if($workshop->is_verified)<i class="bi bi-patch-check-fill text-primary ms-2"></i>@endif</h2>
                            <div class="text-warning">@for($i=1;$i<=5;$i++)<i class="bi {{ $workshop->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor<span class="text-muted ms-2">({{ $workshop->reviews_count }} تقييم)</span></div>
                        </div>
                    </div>
                    @if($workshop->description)<p class="lead">{{ $workshop->description }}</p>@endif
                    @if($workshop->specialties)<div class="mb-3"><strong>التخصصات:</strong> @foreach($workshop->specialties as $s)<span class="badge bg-primary me-1">{{ $s }}</span>@endforeach</div>@endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">الخدمات</h5></div>
                <div class="card-body p-0">
                    @if($workshop->services->isEmpty())<div class="text-center py-4"><p class="text-muted mb-0">لا توجد خدمات محددة</p></div>
                    @else<ul class="list-group list-group-flush">
                        @foreach($workshop->services as $service)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div><strong>{{ $service->name }}</strong>@if($service->description)<br><small class="text-muted">{{ $service->description }}</small>@endif</div>
                                <span class="text-primary fw-bold">{{ $service->price_range }}</span>
                            </li>
                        @endforeach
                    </ul>@endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">التقييمات ({{ $workshop->reviews->count() }})</h5></div>
                <div class="card-body">
                    @forelse($workshop->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between"><strong>{{ $review->user->name }}</strong><small class="text-muted">{{ $review->created_at->diffForHumans() }}</small></div>
                            <div class="text-warning mb-2">@for($i=1;$i<=5;$i++)<i class="bi {{ $review->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor</div>
                            @if($review->comment)<p class="mb-0">{{ $review->comment }}</p>@endif
                        </div>
                    @empty<p class="text-muted text-center mb-0">لا توجد تقييمات بعد</p>@endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">معلومات التواصل</h6>
                    <p class="mb-2"><i class="bi bi-telephone me-2"></i> {{ $workshop->phone }}</p>
                    @if($workshop->email)<p class="mb-2"><i class="bi bi-envelope me-2"></i> {{ $workshop->email }}</p>@endif
                    @if($workshop->whatsapp)<p class="mb-2"><i class="bi bi-whatsapp me-2"></i> <a href="https://wa.me/{{ $workshop->whatsapp }}">واتساب</a></p>@endif
                    <hr>
                    <p class="mb-2"><i class="bi bi-geo-alt me-2"></i> {{ $workshop->address }}</p>
                    <p class="mb-0"><i class="bi bi-pin-map me-2"></i> {{ $workshop->city }}</p>
                </div>
            </div>
            @auth
                <a href="{{ route('maintenance.requests.create') }}?workshop={{ $workshop->id }}" class="btn btn-primary btn-lg w-100"><i class="bi bi-calendar-plus me-2"></i> طلب صيانة</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">سجل دخول لطلب صيانة</a>
            @endauth
        </div>
    </div>
</div>
@endsection
