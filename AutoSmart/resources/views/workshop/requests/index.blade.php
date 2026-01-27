@extends('layouts.app')
@section('title', 'طلبات الصيانة المتاحة')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">طلبات الصيانة المتاحة</h1>
        <a href="{{ route('workshop.dashboard') }}" class="btn btn-outline-secondary">لوحة التحكم</a>
    </div>

    @if($requests->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد طلبات متاحة</h4>
            <p class="text-muted">ستظهر هنا طلبات الصيانة في مدينتك</p>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($requests as $request)
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-{{ $request->urgency === 'high' ? 'danger' : ($request->urgency === 'medium' ? 'warning' : 'secondary') }}">{{ $request->urgency_label }}</span>
                                <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2">{{ Str::limit($request->issue_description, 120) }}</p>
                            @if($request->userCar)
                                <p class="text-muted small mb-1"><i class="bi bi-car-front me-1"></i>{{ $request->userCar->brand?->name }} {{ $request->userCar->model?->name }} {{ $request->userCar->year }}</p>
                            @elseif($request->car_info)
                                <p class="text-muted small mb-1"><i class="bi bi-car-front me-1"></i>{{ $request->car_info }}</p>
                            @endif
                            <p class="text-muted small mb-3"><i class="bi bi-person me-1"></i>{{ $request->user->name }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary small">{{ $request->quotes_count }} عرض سعر مقدم</span>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#quoteModal{{ $request->id }}">تقديم عرض</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quote Modal -->
                <div class="modal fade" id="quoteModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content">
                        <form action="{{ route('workshop.quote', $request) }}" method="POST">@csrf
                            <div class="modal-header"><h5 class="modal-title">تقديم عرض سعر</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="bg-light p-3 rounded mb-3">
                                    <strong>المشكلة:</strong><br>{{ $request->issue_description }}
                                </div>
                                <div class="row g-3">
                                    <div class="col-6"><label class="form-label">تكلفة العمالة</label><input type="number" name="labor_cost" class="form-control" step="0.01" required></div>
                                    <div class="col-6"><label class="form-label">تكلفة القطع</label><input type="number" name="parts_cost" class="form-control" step="0.01" value="0"></div>
                                    <div class="col-6"><label class="form-label">الوقت المقدر (ساعات)</label><input type="number" name="estimated_hours" class="form-control"></div>
                                    <div class="col-6"><label class="form-label">تاريخ التوفر</label><input type="date" name="available_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}"></div>
                                    <div class="col-12"><label class="form-label">تفاصيل العرض</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                                </div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إرسال العرض</button></div>
                        </form>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
