@extends('layouts.app')
@section('title', 'برنامج الشركاء')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="bi bi-share display-1 text-primary"></i>
                <h1 class="h2 mt-3">انضم لبرنامج الشركاء التسويقيين</h1>
                <p class="lead text-muted">اربح عمولات على كل عملية بيع تتم من خلال روابطك</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4 text-center"><div class="card h-100"><div class="card-body"><i class="bi bi-link-45deg display-4 text-primary"></i><h5 class="mt-3">شارك الروابط</h5><p class="text-muted small">أنشئ روابط تسويقية خاصة بك</p></div></div></div>
                <div class="col-md-4 text-center"><div class="card h-100"><div class="card-body"><i class="bi bi-cart-check display-4 text-success"></i><h5 class="mt-3">العملاء يشترون</h5><p class="text-muted small">عندما يشتري شخص من رابطك</p></div></div></div>
                <div class="col-md-4 text-center"><div class="card h-100"><div class="card-body"><i class="bi bi-cash-stack display-4 text-warning"></i><h5 class="mt-3">تحصل عمولة</h5><p class="text-muted small">تحصل على نسبة من كل عملية بيع</p></div></div></div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">تقديم طلب الانضمام</h5></div>
                <div class="card-body">
                    <form action="{{ route('affiliate.apply') }}" method="POST">@csrf
                        <div class="mb-3">
                            <label class="form-label">موقعك الإلكتروني أو حسابك (اختياري)</label>
                            <input type="url" name="website" class="form-control" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نبذة عنك وكيف ستروج للمنتجات</label>
                            <textarea name="bio" class="form-control" rows="4" required placeholder="اشرح كيف ستساعد في الترويج لمنتجاتنا..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">طريقة الدفع المفضلة</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="bank">حوالة بنكية</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تفاصيل الدفع</label>
                            <input type="text" name="payment_details[account]" class="form-control" placeholder="رقم الحساب أو البريد" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">تقديم الطلب</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
