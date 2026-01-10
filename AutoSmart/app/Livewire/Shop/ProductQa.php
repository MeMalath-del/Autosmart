<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductQuestion;

class ProductQa extends Component
{
    public Product $product;
    public string $newQuestion = '';
    public string $newAnswer = '';
    public ?int $answeringQuestionId = null;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function askQuestion()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate(['newQuestion' => 'required|string|max:1000']);

        $this->product->questions()->create([
            'user_id' => auth()->id(),
            'question' => $this->newQuestion
        ]);

        $this->newQuestion = '';
        session()->flash('success', 'تم إرسال سؤالك');
    }

    public function submitAnswer(ProductQuestion $question)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate(['newAnswer' => 'required|string|max:2000']);

        $isSeller = $this->product->store->user_id === auth()->id();

        $question->answers()->create([
            'user_id' => auth()->id(),
            'answer' => $this->newAnswer,
            'is_seller_answer' => $isSeller
        ]);

        if ($isSeller) {
            $question->markAsAnswered();
        }

        $this->newAnswer = '';
        $this->answeringQuestionId = null;
    }

    public function startAnswering(int $questionId)
    {
        $this->answeringQuestionId = $questionId;
    }

    public function render()
    {
        $questions = $this->product->questions()
            ->with(['user', 'answers.user'])
            ->latest()
            ->get();

        return view('livewire.shop.product-qa', [
            'questions' => $questions
        ]);
    }
}
