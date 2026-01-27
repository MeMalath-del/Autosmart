@extends('layouts.app')

@section('title', 'التقسيط')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>خدمة التقسيط</h1>
        <a href="{{ route('installments.calculator') }}" class="btn btn-outline-primary">
            <i class="bi bi-calculator me-1"></i>حاسبة التقسيط
        </a>
    </div>
    
    <!-- Eligibility Status -->
    <div class="card mb-4 {{ $eligibility['eligible'] ? 'border-success' : 'border-warning' }}">
        <div class="card-body">
            <div class="d-flex align-items-center">
                @if($eligibility['eligible'])
                    <span class="bg-success text-white rounded-circle p-3 me-3">
                        <i class="bi bi-check-lg fs-4"></i>
                    </span>
                    <div>
                        <h5 class="mb-1">أنت مؤهل للتقسيط</h5>
                        <p class="text-muted mb-0">الحد الأقصى المتاح: <strong>{{ number_format($eligibility['max_amount']) }} ريال</strong></p>
                    </div>
                @else
                    <span class="bg-warning text-dark rounded-circle p-3 me-3">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </span>
                    <div>
                        <h5 class="mb-1">غير مؤهل للتقسيط حالياً</h5>
                        <p class="text-muted mb-0">{{ implode(' • ', $eligibility['reasons']) }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Active Installments -->
    @php $activeInstallments = $installments->where('status', 'active') @endphp
    @if($activeInstallments->count() > 0)
        <h4 class="mb-3">التقسيطات النشطة</h4>
        <div class="row mb-4">
            @foreach($activeInstallments as $installment)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-success">نشط</span>
                                <span class="text-muted small">{{ $installment->plan->localized_name }}</span>
                            </div>
                            <h5 class="mb-1">{{ number_format($installment->total_amount, 2) }} ريال</h5>
                            <p class="text-muted small">المبلغ الإجمالي</p>
                            
                            <div class="progress mb-3" style="height: 8px;">
                                @php $progress = ($installment->payments_made / $installment->plan->months) * 100 @endphp
                                <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between small text-muted mb-3">
                                <span>{{ $installment->payments_made }} من {{ $installment->plan->months }} أقساط</span>
                                <span>المتبقي: {{ number_format($installment->remaining_amount, 2) }} ريال</span>
                            </div>
                            
                            @if($installment->nextPayment)
                                <div class="alert alert-info py-2 mb-3">
                                    <small>القسط القادم: <strong>{{ number_format($installment->nextPayment->amount, 2) }} ريال</strong></small>
                                    <br>
                                    <small>تاريخ الاستحقاق: {{ $installment->nextPayment->due_date->format('Y/m/d') }}</small>
                                </div>
                            @endif
                            
                            <a href="{{ route('installments.show', $installment) }}" class="btn btn-outline-primary btn-sm w-100">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    <!-- Pending Requests -->
    @php $pendingInstallments = $installments->where('status', 'pending') @endphp
    @if($pendingInstallments->count() > 0)
        <h4 class="mb-3">طلبات قيد المراجعة</h4>
        <div class="table-responsive mb-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>الطلب</th>
                        <th>الخطة</th>
                        <th>المبلغ</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingInstallments as $installment)
                        <tr>
                            <td>#{{ $installment->order_id }}</td>
                            <td>{{ $installment->plan->localized_name }}</td>
                            <td>{{ number_format($installment->total_amount, 2) }} ريال</td>
                            <td>{{ $installment->created_at->format('Y/m/d') }}</td>
                            <td>{!! $installment->status_badge !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <!-- Completed & History -->
    @php $completedInstallments = $installments->whereIn('status', ['completed', 'rejected']) @endphp
    @if($completedInstallments->count() > 0)
        <h4 class="mb-3">السجل</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>الخطة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedInstallments as $installment)
                        <tr>
                            <td>{{ $installment->plan->localized_name }}</td>
                            <td>{{ number_format($installment->total_amount, 2) }} ريال</td>
                            <td>{!! $installment->status_badge !!}</td>
                            <td>{{ $installment->updated_at->format('Y/m/d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <!-- Empty State -->
    @if($installments->count() === 0)
        <div class="text-center py-5">
            <i class="bi bi-credit-card-2-back display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد تقسيطات</h4>
            <p class="text-muted">يمكنك التقسيط عند إتمام طلباتك</p>
            <a href="{{ route('installments.calculator') }}" class="btn btn-primary">
                <i class="bi bi-calculator me-1"></i>جرب حاسبة التقسيط
            </a>
        </div>
    @endif
</div>
@endsection
