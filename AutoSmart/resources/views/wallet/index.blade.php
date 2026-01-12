@extends('layouts.app')

@section('title', 'المحفظة')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Wallet Balance -->
        <div class="col-lg-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-5">
                    <i class="bi bi-wallet2 display-1 mb-3"></i>
                    <h2 class="mb-0">{{ number_format($wallet->balance, 2) }} <small>ريال</small></h2>
                    <p class="text-white-50 mb-0">الرصيد المتاح</p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title mb-3">إجراءات سريعة</h6>
                    <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#topupModal">
                        <i class="bi bi-plus-circle me-2"></i>شحن المحفظة
                    </button>
                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="bi bi-arrow-down-circle me-2"></i>سحب الرصيد
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">آخر المعاملات</h5>
                    <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    @if($transactions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($transactions as $transaction)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex align-items-center">
                                                @if($transaction->isCredit())
                                                    <span class="bg-success text-white rounded-circle p-2 me-3">
                                                        <i class="bi bi-arrow-down"></i>
                                                    </span>
                                                @else
                                                    <span class="bg-danger text-white rounded-circle p-2 me-3">
                                                        <i class="bi bi-arrow-up"></i>
                                                    </span>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $transaction->description }}</h6>
                                                    <small class="text-muted">{{ $transaction->created_at->format('Y/m/d H:i') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="{{ $transaction->isCredit() ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} ريال
                                            </span>
                                            <br>
                                            <small class="text-muted">الرصيد: {{ number_format($transaction->balance_after, 2) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">لا توجد معاملات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Topup Modal -->
<div class="modal fade" id="topupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('wallet.topup') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">شحن المحفظة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ (ريال)</label>
                        <input type="number" name="amount" class="form-control" min="10" max="50000" required>
                        <small class="text-muted">الحد الأدنى 10 ريال، الحد الأقصى 50,000 ريال</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">طريقة الدفع</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="payment_method" value="card" id="pm_card" required>
                                <label class="btn btn-outline-primary w-100" for="pm_card">
                                    <i class="bi bi-credit-card me-1"></i>بطاقة ائتمان
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="payment_method" value="mada" id="pm_mada">
                                <label class="btn btn-outline-primary w-100" for="pm_mada">
                                    <i class="bi bi-credit-card-2-front me-1"></i>مدى
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="payment_method" value="stc_pay" id="pm_stc">
                                <label class="btn btn-outline-primary w-100" for="pm_stc">
                                    STC Pay
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="payment_method" value="apple_pay" id="pm_apple">
                                <label class="btn btn-outline-primary w-100" for="pm_apple">
                                    <i class="bi bi-apple me-1"></i>Apple Pay
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">شحن الآن</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('wallet.withdraw') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">سحب الرصيد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small>الرصيد المتاح للسحب: <strong>{{ number_format($wallet->balance, 2) }} ريال</strong></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ (ريال)</label>
                        <input type="number" name="amount" class="form-control" min="100" max="{{ $wallet->balance }}" required>
                        <small class="text-muted">الحد الأدنى للسحب 100 ريال</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اسم البنك</label>
                        <select name="bank_name" class="form-select" required>
                            <option value="">اختر البنك</option>
                            <option value="الراجحي">مصرف الراجحي</option>
                            <option value="الأهلي">البنك الأهلي</option>
                            <option value="الرياض">بنك الرياض</option>
                            <option value="الإنماء">مصرف الإنماء</option>
                            <option value="البلاد">بنك البلاد</option>
                            <option value="السعودي الفرنسي">البنك السعودي الفرنسي</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اسم صاحب الحساب</label>
                        <input type="text" name="account_holder_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الآيبان (IBAN)</label>
                        <input type="text" name="iban" class="form-control" placeholder="SA0000000000000000000000" maxlength="24">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الحساب</label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">طلب السحب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
