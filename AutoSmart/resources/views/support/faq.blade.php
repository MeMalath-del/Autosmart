@extends('layouts.app')
@section('title', 'الأسئلة الشائعة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-question-circle me-2"></i>الأسئلة الشائعة</h1>

    @foreach($faqs as $category => $items)
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">{{ $category ?? 'عام' }}</h5></div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="faq{{ $loop->index }}">
                    @foreach($items as $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faq{{ $loop->parent->index }}">
                                <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="text-center mt-4">
        <p class="text-muted">لم تجد إجابة لسؤالك؟</p>
        <a href="{{ route('support.create') }}" class="btn btn-primary">تواصل معنا</a>
    </div>
</div>
@endsection
