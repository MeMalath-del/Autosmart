@extends('layouts.app')
@section('title', 'البحث المتقدم')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-search me-2"></i>البحث المتقدم</h1>
    @livewire('shop.advanced-search')
</div>
@endsection
