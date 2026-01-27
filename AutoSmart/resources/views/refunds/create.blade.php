@extends('layouts.app')
@section('title', 'طلب استرداد')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">طلب استرداد للطلب #{{ $order->order_number }}</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('refunds.store') }}" method="POST">@csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">سبب الاسترداد</label>
                            <textarea name="reason" class="form-control" rows="4" required placeholder="اشرح سبب طلب الاسترداد..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">طريقة الاسترداد</label>
                            <div class="form-check">
                                <input type="radio" name="refund_type" value="wallet" class="form-check-input" id="typeWallet" checked>
                                <label class="form-check-label" for="typeWallet">رصيد المحفظة (فوري)</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="refund_type" value="bank" class="form-check-input" id="typeBank">
                                <label class="form-check-label" for="typeBank">تحويل بنكي (3-5 أيام عمل)</label>
                            </div>
                        </div>

                        <div id="bankDetails" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">اسم البنك</label><input type="text" name="bank_name" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label">رقم الحساب</label><input type="text" name="account_number" class="form-control"></div>
                                <div class="col-12"><label class="form-label">رقم الآيبان (اختياري)</label><input type="text" name="iban" class="form-control" placeholder="SA..."></div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">إرسال طلب الاسترداد</button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6>ملخص الطلب</h6>
                    <hr>
                    <p><strong>إجمالي الطلب:</strong> {{ number_format($order->total, 2) }} ر.س</p>
                    <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y/m/d') }}</p>
                    <p><strong>الحالة:</strong> {{ $order->status_label }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('[name="refund_type"]').forEach(el => {
    el.addEventListener('change', function() {
        document.getElementById('bankDetails').style.display = this.value === 'bank' ? 'block' : 'none';
    });
});
</script>
@endsection
