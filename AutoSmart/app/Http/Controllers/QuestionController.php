<?php

namespace App\Http\Controllers;

use App\Models\ProductQuestion;
use App\Models\ProductAnswer;
use App\Models\Product;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate(['question' => 'required|string|max:1000']);
        
        $product->questions()->create([
            'user_id' => auth()->id(),
            'question' => $request->question
        ]);

        return back()->with('success', 'تم إرسال سؤالك');
    }

    public function answer(Request $request, ProductQuestion $question)
    {
        $request->validate(['answer' => 'required|string|max:2000']);
        
        $isSeller = $question->product->store->user_id === auth()->id();
        
        $question->answers()->create([
            'user_id' => auth()->id(),
            'answer' => $request->answer,
            'is_seller_answer' => $isSeller
        ]);

        if ($isSeller) {
            $question->markAsAnswered();
        }

        return back()->with('success', 'تم إرسال إجابتك');
    }

    public function markBest(ProductAnswer $answer)
    {
        if ($answer->question->product->store->user_id !== auth()->id()) {
            abort(403);
        }
        
        $answer->markAsBest();
        return back()->with('success', 'تم تحديد الإجابة المميزة');
    }

    public function vote(Request $request, string $type, int $id)
    {
        $model = $type === 'question' ? ProductQuestion::find($id) : ProductAnswer::find($id);
        if (!$model) abort(404);

        $model->votes()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['is_helpful' => $request->boolean('helpful', true)]
        );

        return back();
    }
}
