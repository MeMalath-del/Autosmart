<div class="mt-5">
    <h4 class="mb-4"><i class="bi bi-chat-left-quote me-2"></i>الأسئلة والإجابات</h4>

    @auth
        <div class="card mb-4">
            <div class="card-body">
                <form wire:submit="askQuestion">
                    <div class="mb-3">
                        <label class="form-label">هل لديك سؤال؟</label>
                        <textarea wire:model="newQuestion" class="form-control" rows="2" placeholder="اكتب سؤالك هنا..."></textarea>
                        @error('newQuestion') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">إرسال السؤال</button>
                </form>
            </div>
        </div>
    @endauth

    @forelse($questions as $question)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            <i class="bi bi-question"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $question->user->name }}</strong>
                            <small class="text-muted">{{ $question->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 mt-2">{{ $question->question }}</p>
                    </div>
                </div>

                @foreach($question->answers as $answer)
                    <div class="d-flex mb-2 me-5">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-{{ $answer->is_seller_answer ? 'success' : 'secondary' }} text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <i class="bi bi-{{ $answer->is_seller_answer ? 'shop' : 'person' }}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <div class="bg-light rounded p-2">
                                <small class="text-muted">
                                    {{ $answer->user->name }}
                                    @if($answer->is_seller_answer)<span class="badge bg-success ms-1">البائع</span>@endif
                                    @if($answer->is_best_answer)<span class="badge bg-warning ms-1">أفضل إجابة</span>@endif
                                </small>
                                <p class="mb-0 small">{{ $answer->answer }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                @auth
                    @if($answeringQuestionId === $question->id)
                        <form wire:submit="submitAnswer({{ $question->id }})" class="mt-3 me-5">
                            <div class="input-group">
                                <input wire:model="newAnswer" type="text" class="form-control" placeholder="أضف إجابتك...">
                                <button class="btn btn-success" type="submit">إرسال</button>
                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('answeringQuestionId', null)">إلغاء</button>
                            </div>
                        </form>
                    @else
                        <button wire:click="startAnswering({{ $question->id }})" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-reply me-1"></i>إضافة إجابة
                        </button>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-muted">
            <i class="bi bi-chat-left display-4"></i>
            <p class="mt-2">لا توجد أسئلة بعد. كن أول من يسأل!</p>
        </div>
    @endforelse
</div>
