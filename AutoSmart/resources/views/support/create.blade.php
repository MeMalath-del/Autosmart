@extends('layouts.app')
@section('title', 'تذكرة دعم جديدة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-plus-circle me-2"></i>تذكرة دعم جديدة</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('support.store') }}" method="POST">@csrf
                        <div class="mb-3">
                            <label class="form-label">الموضوع</label>
                            <input type="text" name="subject" class="form-control" required placeholder="عنوان مختصر للمشكلة">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">الفئة</label>
                                <select name="category" class="form-select" required>
                                    <option value="order">الطلبات</option>
                                    <option value="payment">الدفع</option>
                                    <option value="shipping">الشحن والتوصيل</option>
                                    <option value="product">المنتجات</option>
                                    <option value="account">الحساب</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الأولوية</label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">منخفضة</option>
                                    <option value="medium" selected>متوسطة</option>
                                    <option value="high">عالية</option>
                                    <option value="urgent">عاجلة</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الطلب (اختياري)</label>
                            <input type="text" name="order_id" class="form-control" placeholder="إذا كانت المشكلة متعلقة بطلب محدد">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تفاصيل المشكلة</label>
                            <textarea name="message" class="form-control" rows="6" required placeholder="اشرح المشكلة بالتفصيل..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">إرسال التذكرة</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @if($faqs->count())
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">أسئلة شائعة</h6></div>
                    <div class="card-body p-0">
                        <div class="accordion accordion-flush" id="faqAccordion">
                            @foreach($faqs->flatten()->take(5) as $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">{{ $faq->question }}</button></h2>
                                    <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body small">{{ $faq->answer }}</div></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
