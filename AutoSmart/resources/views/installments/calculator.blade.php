@extends('layouts.app')

@section('title', 'حاسبة التقسيط')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="bi bi-calculator display-1 text-primary"></i>
                <h1 class="mt-3">حاسبة التقسيط</h1>
                <p class="text-muted">احسب قيمة أقساطك الشهرية بسهولة</p>
            </div>
            
            <div class="card">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label">المبلغ الإجمالي (ريال)</label>
                        <input type="number" id="amount" class="form-control form-control-lg" 
                               placeholder="أدخل المبلغ" min="500" max="100000" value="5000">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">خطة التقسيط</label>
                        <div class="row g-2">
                            @foreach($plans as $plan)
                                <div class="col-md-4 col-6">
                                    <input type="radio" class="btn-check" name="plan" value="{{ $plan->id }}" 
                                           id="plan_{{ $plan->id }}" {{ $loop->first ? 'checked' : '' }}
                                           data-months="{{ $plan->months }}"
                                           data-interest="{{ $plan->interest_rate }}"
                                           data-down-payment="{{ $plan->down_payment_percentage }}">
                                    <label class="btn btn-outline-primary w-100 py-3" for="plan_{{ $plan->id }}">
                                        <strong>{{ $plan->months }} شهر</strong>
                                        @if($plan->interest_rate > 0)
                                            <br><small>{{ $plan->interest_rate }}% فائدة</small>
                                        @else
                                            <br><small class="text-success">بدون فوائد</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div id="result" class="bg-light rounded p-4">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <small class="text-muted d-block">الدفعة الأولى</small>
                                <h4 class="text-primary mb-0" id="downPayment">0</h4>
                                <small>ريال</small>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <small class="text-muted d-block">القسط الشهري</small>
                                <h3 class="text-success mb-0" id="monthlyPayment">0</h3>
                                <small>ريال</small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">المبلغ الإجمالي</small>
                                <h4 class="mb-0" id="totalAmount">0</h4>
                                <small>ريال</small>
                            </div>
                        </div>
                    </div>
                    
                    @auth
                        <div class="text-center mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart me-2"></i>تسوق الآن واستفد من التقسيط
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mt-4 text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            <a href="{{ route('login') }}">سجل دخولك</a> للاستفادة من خدمة التقسيط
                        </div>
                    @endauth
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>شروط التقسيط</h5>
                    <ul class="mb-0">
                        <li>الحد الأدنى للتقسيط: 500 ريال</li>
                        <li>يجب أن يكون لديك حساب نشط في المنصة</li>
                        <li>تتطلب الموافقة على التقسيط مراجعة الطلب</li>
                        <li>الأقساط الشهرية ثابتة طوال فترة التقسيط</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateInstallment() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const selectedPlan = document.querySelector('input[name="plan"]:checked');
    
    if (!selectedPlan || amount < 500) return;
    
    const months = parseInt(selectedPlan.dataset.months);
    const interestRate = parseFloat(selectedPlan.dataset.interestRate || 0);
    const downPaymentPercentage = parseFloat(selectedPlan.dataset.downPayment || 0);
    
    const downPayment = amount * (downPaymentPercentage / 100);
    const financedAmount = amount - downPayment;
    
    let monthlyPayment;
    let totalWithInterest;
    
    if (interestRate === 0) {
        monthlyPayment = financedAmount / months;
        totalWithInterest = amount;
    } else {
        const monthlyRate = interestRate / 100 / 12;
        monthlyPayment = (financedAmount * monthlyRate * Math.pow(1 + monthlyRate, months)) / 
                        (Math.pow(1 + monthlyRate, months) - 1);
        totalWithInterest = downPayment + (monthlyPayment * months);
    }
    
    document.getElementById('downPayment').textContent = downPayment.toFixed(2);
    document.getElementById('monthlyPayment').textContent = monthlyPayment.toFixed(2);
    document.getElementById('totalAmount').textContent = totalWithInterest.toFixed(2);
}

document.getElementById('amount').addEventListener('input', calculateInstallment);
document.querySelectorAll('input[name="plan"]').forEach(radio => {
    radio.addEventListener('change', calculateInstallment);
});

// Initial calculation
calculateInstallment();
</script>
@endsection
