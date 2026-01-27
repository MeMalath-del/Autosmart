@extends('layouts.app')
@section('title', 'الاشتراكات')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-arrow-repeat me-2"></i>خطط الاشتراك</h1>

    <div class="row g-4 justify-content-center mb-5">
        @foreach($plans as $plan)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 {{ $plan->is_featured ? 'border-primary' : '' }}">
                    @if($plan->is_featured)<div class="card-header bg-primary text-white text-center">الأكثر شعبية</div>@endif
                    <div class="card-body text-center">
                        <h4>{{ $plan->localized_name }}</h4>
                        <p class="text-muted">{{ $plan->cycle_label }}</p>
                        <h2 class="text-primary my-3">{{ number_format($plan->price, 2) }} <small class="fs-6">ر.س</small></h2>
                        
                        @if($plan->features)
                            <ul class="list-unstyled text-start">
                                @foreach($plan->features as $feature)
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif
                        
                        <form action="{{ route('subscriptions.subscribe', $plan) }}" method="POST">@csrf
                            <button type="submit" class="btn btn-{{ $plan->is_featured ? 'primary' : 'outline-primary' }} btn-lg w-100 mt-3">اشترك الآن</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($userSubscriptions->count())
        <h4 class="mb-3">اشتراكاتي</h4>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>الخطة</th><th>الحالة</th><th>تاريخ البدء</th><th>تاريخ الانتهاء</th><th></th></tr></thead>
                    <tbody>
                        @foreach($userSubscriptions as $sub)
                            <tr>
                                <td>{{ $sub->plan->name }}</td>
                                <td><span class="badge bg-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ $sub->status }}</span></td>
                                <td>{{ $sub->starts_at->format('Y/m/d') }}</td>
                                <td>{{ $sub->ends_at?->format('Y/m/d') ?? '-' }}</td>
                                <td><a href="{{ route('subscriptions.show', $sub) }}" class="btn btn-sm btn-outline-primary">إدارة</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>
        </div>
    @endif
</div>
@endsection
