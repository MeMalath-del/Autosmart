<div class="review-form">
    @if($existingReview)
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            لقد قمت بتقييم هذا المنتج سابقاً
        </div>
    @endif

    @if(!$showForm)
        <button type="button" class="btn btn-outline-primary" wire:click="toggleForm">
            <i class="bi bi-star me-2"></i>
            {{ $existingReview ? 'تعديل التقييم' : 'أضف تقييمك' }}
        </button>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">تقييم المنتج</h6>
                <button type="button" class="btn-close" wire:click="toggleForm"></button>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="submitReview">
                    <div class="mb-4">
                        <label class="form-label">التقييم العام</label>
                        <div class="rating-stars fs-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $rating >= $i ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" 
                                   style="cursor: pointer;"
                                   wire:click="$set('rating', {{ $i }})"></i>
                            @endfor
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">جودة المنتج</label>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $quality_rating >= $i ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" 
                                       style="cursor: pointer;"
                                       wire:click="$set('quality_rating', {{ $i }})"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">السعر</label>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $price_rating >= $i ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" 
                                       style="cursor: pointer;"
                                       wire:click="$set('price_rating', {{ $i }})"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الشحن</label>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $shipping_rating >= $i ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" 
                                       style="cursor: pointer;"
                                       wire:click="$set('shipping_rating', {{ $i }})"></i>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تعليقك (اختياري)</label>
                        <textarea class="form-control" rows="3" wire:model="comment" 
                                  placeholder="شاركنا تجربتك مع هذا المنتج..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">صور (اختياري)</label>
                        <input type="file" class="form-control" wire:model="images" multiple accept="image/*">
                        <div class="form-text">يمكنك رفع حتى 5 صور</div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="submitReview">إرسال التقييم</span>
                        <span wire:loading wire:target="submitReview">جاري الإرسال...</span>
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
