<div>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0"><i class="bi bi-columns-gap me-2"></i>مقارنة المنتجات</h2>
            @if(count($products) > 0)
                <button wire:click="clearAll" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>مسح الكل</button>
            @endif
        </div>

        @if(count($products) === 0)
            <div class="card"><div class="card-body text-center py-5">
                <i class="bi bi-columns-gap display-1 text-muted"></i>
                <h5 class="mt-3">لم تقم بإضافة منتجات للمقارنة</h5>
                <p class="text-muted">أضف منتجات من صفحات المنتجات لمقارنتها</p>
            </div></div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="width:150px;"></th>
                                @foreach($products as $product)
                                    <th class="text-center" style="min-width:200px;">
                                        <button wire:click="removeProduct({{ $product->id }})" class="btn btn-sm btn-link text-danger float-start"><i class="bi bi-x-lg"></i></button>
                                        <img src="{{ $product->primary_image_url }}" class="rounded mb-2" style="width:80px;height:80px;object-fit:cover;">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary mt-2">عرض المنتج</a>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attributes as $label => $getter)
                                <tr>
                                    <th class="bg-light">{{ $label }}</th>
                                    @foreach($products as $product)
                                        <td class="text-center">{{ $getter($product) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <th class="bg-light">إضافة للسلة</th>
                                @foreach($products as $product)
                                    <td class="text-center">
                                        @if($product->quantity > 0)
                                            <form action="{{ route('cart.add', $product) }}" method="POST">@csrf<button class="btn btn-sm btn-primary"><i class="bi bi-cart-plus me-1"></i>أضف</button></form>
                                        @else
                                            <span class="text-muted">غير متوفر</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>يمكنك مقارنة حتى {{ $maxProducts }} منتجات في نفس الوقت
            </div>
        @endif
    </div>
</div>
