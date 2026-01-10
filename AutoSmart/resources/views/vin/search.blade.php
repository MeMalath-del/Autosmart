@extends('layouts.app')
@section('title', 'البحث بالشاسيه')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <i class="bi bi-upc-scan display-1 text-primary mb-3"></i>
            <h1 class="h2 mb-3">البحث برقم الشاسيه (VIN)</h1>
            <p class="text-muted mb-4">أدخل رقم الشاسيه المكون من 17 خانة للعثور على قطع الغيار المتوافقة مع سيارتك</p>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('vin.search.submit') }}" method="POST">@csrf
                        <div class="input-group input-group-lg mb-3">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="vin" class="form-control text-center font-monospace" 
                                   placeholder="أدخل رقم الشاسيه VIN" maxlength="17" pattern="[A-HJ-NPR-Z0-9]{17}" 
                                   style="letter-spacing: 2px;" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg px-5">بحث</button>
                    </form>
                </div>
            </div>

            <div class="mt-5 text-start">
                <h5>أين أجد رقم الشاسيه؟</h5>
                <ul class="text-muted">
                    <li>على لوحة صغيرة أسفل الزجاج الأمامي (جهة السائق)</li>
                    <li>على ملصق إطار الباب الأمامي (جهة السائق)</li>
                    <li>في استمارة السيارة</li>
                    <li>في شهادة التأمين</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
