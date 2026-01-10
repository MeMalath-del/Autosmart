@extends('layouts.seller')

@section('title', 'تفاصيل طلب الضمان')

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.warranty.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right me-1"></i> العودة لطلبات الضمان
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $warrantyClaim->claim_number }}</h5>
                <span class="badge bg-{{ $warrantyClaim->status_color }} fs-6">
                    {{ $warrantyClaim->status_label }}
                </span>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-4 p-3 bg-light rounded">
                    <img src="{{ $warrantyClaim->product->main_image }}" 
                         alt="{{ $warrantyClaim->product->name }}"
                         class="rounded"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h6 class="mb-1">{{ $warrantyClaim->product->name }}</h6>
                        <p class="text-muted small mb-1">
                            رقم الطلب: <a href="{{ route('seller.orders.show', $warrantyClaim->order) }}">{{ $warrantyClaim->order->order_number }}</a>
                        </p>
                        <p class="text-muted small mb-0">
                            تاريخ الشراء: {{ $warrantyClaim->order->created_at->format('Y/m/d') }}
                        </p>
                    </div>
                </div>

                <h6>وصف المشكلة:</h6>
                <p class="bg-light p-3 rounded">{{ $warrantyClaim->issue_description }}</p>

                @if($warrantyClaim->images)
                    <h6>الصور المرفقة:</h6>
                    <div class="row g-2 mb-4">
                        @foreach($warrantyClaim->images as $image)
                            <div class="col-4 col-md-3">
                                <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="صورة"
                                         class="img-fluid rounded">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($warrantyClaim->status !== 'closed')
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الرد على المطالبة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('seller.warranty.respond', $warrantyClaim) }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">القرار <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="under_review" {{ old('status', $warrantyClaim->status) === 'under_review' ? 'selected' : '' }}>تحت المراجعة</option>
                                    <option value="approved" {{ old('status', $warrantyClaim->status) === 'approved' ? 'selected' : '' }}>موافقة</option>
                                    <option value="rejected" {{ old('status', $warrantyClaim->status) === 'rejected' ? 'selected' : '' }}>رفض</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الحل (في حالة الموافقة)</label>
                                <select name="resolution" class="form-select">
                                    <option value="">اختر...</option>
                                    <option value="repair" {{ old('resolution', $warrantyClaim->resolution) === 'repair' ? 'selected' : '' }}>إصلاح</option>
                                    <option value="replace" {{ old('resolution', $warrantyClaim->resolution) === 'replace' ? 'selected' : '' }}>استبدال</option>
                                    <option value="refund" {{ old('resolution', $warrantyClaim->resolution) === 'refund' ? 'selected' : '' }}>استرداد المبلغ</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">الرد <span class="text-danger">*</span></label>
                                <textarea name="store_response" class="form-control @error('store_response') is-invalid @enderror" 
                                          rows="4" required>{{ old('store_response', $warrantyClaim->store_response) }}</textarea>
                                @error('store_response')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i> إرسال الرد
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">معلومات العميل</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>الاسم:</strong> {{ $warrantyClaim->user->name }}</p>
                <p class="mb-2"><strong>البريد:</strong> {{ $warrantyClaim->user->email }}</p>
                @if($warrantyClaim->user->phone)
                    <p class="mb-0"><strong>الجوال:</strong> {{ $warrantyClaim->user->phone }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
