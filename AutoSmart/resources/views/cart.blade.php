@extends('layouts.app')

@section('title', 'سلة التسوق')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-cart3 me-2"></i>سلة التسوق</h2>
    
    @livewire('shop.cart')
</div>
@endsection
