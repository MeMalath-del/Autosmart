<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-badge-ar me-2"></i>عرض بتقنية الواقع المعزز</h5>
        </div>
        <div class="card-body text-center">
            <div id="ar-container" class="bg-light rounded p-5 mb-3" style="min-height:300px;">
                <div id="ar-placeholder">
                    <i class="bi bi-badge-ar display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">الواقع المعزز</h5>
                    <p class="text-muted">شاهد كيف ستبدو هذه القطعة في سيارتك</p>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                لاستخدام الواقع المعزز، وجه كاميرا هاتفك نحو المكان الذي تريد رؤية القطعة فيه
            </div>
            
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary btn-lg" onclick="startAR()">
                    <i class="bi bi-camera me-2"></i>بدء تجربة AR
                </button>
                <a href="{{ $product->primary_image_url }}" download class="btn btn-outline-secondary">
                    <i class="bi bi-download me-2"></i>تحميل صورة المنتج
                </a>
            </div>
            
            <div class="mt-4">
                <h6>المواصفات للعرض ثلاثي الأبعاد:</h6>
                <div class="row g-2 justify-content-center">
                    <div class="col-auto"><span class="badge bg-secondary">الطول: {{ $product->dimensions['length'] ?? 'N/A' }} سم</span></div>
                    <div class="col-auto"><span class="badge bg-secondary">العرض: {{ $product->dimensions['width'] ?? 'N/A' }} سم</span></div>
                    <div class="col-auto"><span class="badge bg-secondary">الارتفاع: {{ $product->dimensions['height'] ?? 'N/A' }} سم</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startAR() {
    // Check for AR support
    if ('xr' in navigator) {
        navigator.xr.isSessionSupported('immersive-ar').then((supported) => {
            if (supported) {
                // Start AR session
                alert('جاري تحميل تجربة الواقع المعزز...');
            } else {
                alert('عذراً، جهازك لا يدعم تقنية الواقع المعزز');
            }
        });
    } else if (window.matchMedia('(pointer: coarse)').matches) {
        // Mobile fallback - show in camera view simulation
        alert('سيتم فتح الكاميرا لتجربة الواقع المعزز. هذه الميزة تتطلب تطبيق AR مخصص.');
    } else {
        alert('للحصول على أفضل تجربة AR، استخدم هاتفك الذكي');
    }
}
</script>
