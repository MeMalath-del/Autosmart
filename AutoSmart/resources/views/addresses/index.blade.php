@extends('layouts.app')

@section('title', 'عناوين التوصيل')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">عناوين التوصيل</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bi bi-plus-lg me-1"></i> إضافة عنوان
                </button>
            </div>

            @if($addresses->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-geo-alt display-1 text-muted"></i>
                        <h4 class="mt-3">لا توجد عناوين محفوظة</h4>
                        <p class="text-muted">أضف عنوانك الأول لتسهيل عملية الشراء</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            إضافة عنوان
                        </button>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($addresses as $address)
                        <div class="col-md-6">
                            <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-secondary">{{ $address->label }}</span>
                                        @if($address->is_default)
                                            <span class="badge bg-primary">الافتراضي</span>
                                        @endif
                                    </div>
                                    <h6 class="mb-1">{{ $address->name }}</h6>
                                    <p class="text-muted small mb-1">{{ $address->phone }}</p>
                                    <p class="text-muted small mb-3">{{ $address->full_address }}</p>
                                    <div class="d-flex gap-2">
                                        @if(!$address->is_default)
                                            <form action="{{ route('addresses.default', $address) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    تعيين كافتراضي
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="ms-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('هل تريد حذف هذا العنوان؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة عنوان جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">التسمية</label>
                        <select name="label" class="form-select">
                            <option value="المنزل">المنزل</option>
                            <option value="العمل">العمل</option>
                            <option value="أخرى">أخرى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الجوال</label>
                        <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المدينة</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحي</label>
                        <input type="text" name="district" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الرمز البريدي</label>
                        <input type="text" name="postal_code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان التفصيلي</label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ العنوان</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
