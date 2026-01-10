<button type="button" 
        class="btn btn-sm {{ $isInComparison ? 'btn-primary' : 'btn-outline-secondary' }}"
        wire:click="toggle"
        title="{{ $isInComparison ? 'إزالة من المقارنة' : 'إضافة للمقارنة' }}">
    <span wire:loading.remove wire:target="toggle">
        <i class="bi {{ $isInComparison ? 'bi-check-lg' : 'bi-columns-gap' }}"></i>
    </span>
    <span wire:loading wire:target="toggle">
        <span class="spinner-border spinner-border-sm"></span>
    </span>
</button>
