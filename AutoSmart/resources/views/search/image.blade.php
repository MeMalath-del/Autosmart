@extends('layouts.app')

@section('title', 'البحث بالصور')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="bi bi-image display-1 text-primary"></i>
                <h1 class="mt-3">البحث بالصور</h1>
                <p class="text-muted">قم بتحميل صورة لقطعة الغيار وسنساعدك في العثور عليها</p>
            </div>
            
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('search.image.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4 text-center">
                            <div id="image-preview" class="mb-3" style="display: none;">
                                <img src="" id="preview-img" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                            
                            <label for="image" class="btn btn-lg btn-outline-primary">
                                <i class="bi bi-cloud-upload me-2"></i>اختر صورة
                            </label>
                            <input type="file" name="image" id="image" class="d-none" accept="image/*" required>
                            <p class="text-muted small mt-2">الحد الأقصى لحجم الصورة: 5 ميجابايت</p>
                        </div>
                        
                        @error('image')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search me-2"></i>بحث
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-lightbulb me-2"></i>نصائح للحصول على نتائج أفضل</h5>
                    <ul class="mb-0">
                        <li>استخدم صورة واضحة وعالية الجودة</li>
                        <li>تأكد من أن القطعة ظاهرة بشكل كامل في الصورة</li>
                        <li>تجنب الصور المعتمة أو الضبابية</li>
                        <li>يفضل أن تكون القطعة على خلفية بسيطة</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
