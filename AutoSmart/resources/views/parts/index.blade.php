@extends('layouts.app')
@section('title', 'تتبع القطع المركبة')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-clock-history me-2"></i>تتبع القطع المركبة</h1>
        <a href="{{ route('parts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>تسجيل قطعة</a>
    </div>

    @if($expiring > 0)
        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>لديك {{ $expiring }} قطع تحتاج استبدال قريباً</div>
    @endif

    @if($parts->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-wrench display-1 text-muted"></i>
            <h4 class="mt-3">لم تسجل قطع مركبة بعد</h4>
            <p class="text-muted">سجل القطع المركبة في سيارتك لتتبع عمرها ومواعيد استبدالها</p>
            <a href="{{ route('parts.create') }}" class="btn btn-primary">تسجيل قطعة</a>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($parts as $part)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 {{ $part->status === 'warning' ? 'border-warning' : ($part->status === 'expired' ? 'border-danger' : '') }}">
                        <div class="card-header d-flex justify-content-between">
                            <span>{{ $part->product->name }}</span>
                            <span class="badge bg-{{ $part->status === 'active' ? 'success' : ($part->status === 'replaced' ? 'secondary' : ($part->status === 'warning' ? 'warning' : 'danger')) }}">{{ $part->status === 'active' ? 'نشط' : ($part->status === 'replaced' ? 'تم استبداله' : ($part->status === 'warning' ? 'قريب الاستبدال' : 'منتهي')) }}</span>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-1"><i class="bi bi-car-front me-1"></i>{{ $part->userCar->carModel->brand->name }} {{ $part->userCar->carModel->name }}</p>
                            <p class="small mb-1"><i class="bi bi-calendar me-1"></i>تركيب: {{ $part->installation_date->format('Y/m/d') }}</p>
                            @if($part->expected_replacement_date)
                                <p class="small {{ $part->expected_replacement_date->isPast() ? 'text-danger' : '' }}"><i class="bi bi-calendar-x me-1"></i>استبدال متوقع: {{ $part->expected_replacement_date->format('Y/m/d') }}</p>
                            @endif
                            @if($part->installation_mileage)
                                <p class="small"><i class="bi bi-speedometer2 me-1"></i>عداد التركيب: {{ number_format($part->installation_mileage) }} كم</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $parts->links() }}</div>
    @endif
</div>
@endsection
