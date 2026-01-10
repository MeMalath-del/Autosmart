@extends('layouts.app')

@section('title', 'إتمام الشراء')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-credit-card me-2"></i>إتمام الشراء</h2>
    
    @livewire('shop.checkout')
</div>
@endsection
