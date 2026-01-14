<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // أسئلة المنتجات
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
        });

        // إجابات الأسئلة
        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('product_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('answer');
            $table->boolean('is_seller_answer')->default(false);
            $table->boolean('is_best_answer')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // تصويتات الأسئلة والإجابات
        Schema::create('qa_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('voteable');
            $table->boolean('is_helpful')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'voteable_type', 'voteable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_votes');
        Schema::dropIfExists('product_answers');
        Schema::dropIfExists('product_questions');
    }
};
