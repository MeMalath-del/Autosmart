<button wire:click="toggle" class="wishlist-btn {{ $isInWishlist ? 'active' : '' }}" title="{{ $isInWishlist ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}">
    <i class="bi bi-heart{{ $isInWishlist ? '-fill' : '' }}"></i>
</button>
