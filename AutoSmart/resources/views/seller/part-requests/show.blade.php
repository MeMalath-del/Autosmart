@extends('layouts.seller')

@section('title', 'تفاصيل طلب القطعة')

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.part-requests.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right me-1"></i> العودة للطلبات
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $partRequest->part_name }}</h5>
                <span class="badge bg-{{ $partRequest->urgency === 'high' ? 'danger' : ($partRequest->urgency === 'medium' ? 'warning' : 'secondary') }}">
                    {{ $partRequest->urgency_label }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>العميل:</strong> {{ $partRequest->user->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ الطلب:</strong> {{ $partRequest->created_at->format('Y/m/d') }}
                    </div>
                </div>

                @if($partRequest->carBrand)
                    <div class="mb-3">
                        <strong>السيارة:</strong>
                        {{ $partRequest->carBrand->name }}
                        @if($partRequest->carModel) - {{ $partRequest->carModel->name }} @endif
                        @if($partRequest->car_year) ({{ $partRequest->car_year }}) @endif
                    </div>
                @endif

                @if($partRequest->part_number)
                    <div class="mb-3">
                        <strong>رقم القطعة:</strong> {{ $partRequest->part_number }}
                    </div>
                @endif

                @if($partRequest->description)
                    <div class="mb-3">
                        <strong>الوصف:</strong>
                        <p class="mb-0 bg-light p-3 rounded">{{ $partRequest->description }}</p>
                    </div>
                @endif

                @if($partRequest->budget_min || $partRequest->budget_max)
                    <div class="mb-3">
                        <strong>الميزانية المتوقعة:</strong>
                        @if($partRequest->budget_min && $partRequest->budget_max)
                            {{ number_format($partRequest->budget_min) }} - {{ number_format($partRequest->budget_max) }} ر.س
                        @elseif($partRequest->budget_max)
                            حتى {{ number_format($partRequest->budget_max) }} ر.س
                        @else
                            من {{ number_format($partRequest->budget_min) }} ر.س
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $myQuote ? 'تعديل العرض' : 'تقديم عرض' }}</h5>
            </div>
            <div class="card-body">
                @if($partRequest->status === 'closed')
                    <div class="alert alert-secondary">
                        <i class="bi bi-info-circle me-1"></i> هذا الطلب مغلق
                    </div>
                @else
                    <form action="{{ route('seller.part-requests.quote', $partRequest) }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">السعر (ر.س) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', $myQuote?->price) }}" step="0.01" min="0" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">حالة القطعة <span class="text-danger">*</span></label>
                                <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                    <option value="new" {{ old('condition', $myQuote?->condition) === 'new' ? 'selected' : '' }}>جديد</option>
                                    <option value="used" {{ old('condition', $myQuote?->condition) === 'used' ? 'selected' : '' }}>مستعمل</option>
                                    <option value="refurbished" {{ old('condition', $myQuote?->condition) === 'refurbished' ? 'selected' : '' }}>مجدد</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الضمان</label>
                                <input type="text" name="warranty" class="form-control"
                                       value="{{ old('warranty', $myQuote?->warranty) }}"
                                       placeholder="مثال: سنة واحدة">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مدة التوصيل (بالأيام)</label>
                                <input type="number" name="delivery_days" class="form-control"
                                       value="{{ old('delivery_days', $myQuote?->delivery_days) }}" min="1" max="30">
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3"
                                          placeholder="أي معلومات إضافية عن القطعة...">{{ old('notes', $myQuote?->notes) }}</textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i> 
                                    {{ $myQuote ? 'تحديث العرض' : 'إرسال العرض' }}
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($partRequest->images)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">الصور المرفقة</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($partRequest->images as $image)
                            <div class="col-6">
                                <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="صورة"
                                         class="img-fluid rounded">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">العروض الأخرى ({{ $partRequest->quotes->count() }})</h6>
            </div>
            <div class="card-body p-0">
                @if($partRequest->quotes->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">كن أول من يقدم عرضاً!</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($partRequest->quotes as $quote)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ $quote->store->name }}</span>
                                    <span class="fw-bold">{{ number_format($quote->price, 2) }} ر.س</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
