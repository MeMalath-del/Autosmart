@extends('layouts.app')

@section('title', 'محادثة مع ' . $conversation->store->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('conversations.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('stores.show', $conversation->store) }}" class="text-dark">
                                    {{ $conversation->store->localized_name }}
                                </a>
                            </h6>
                            @if($conversation->product)
                                <small class="text-muted">{{ $conversation->product->name }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <livewire:shop.messages :conversation="$conversation" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
