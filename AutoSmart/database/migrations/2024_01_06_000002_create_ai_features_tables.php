<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // البحث بالصور
        Schema::create('image_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('image_path');
            $table->string('image_hash')->nullable();
            $table->json('detected_features')->nullable();
            $table->json('matched_products')->nullable();
            $table->integer('results_count')->default(0);
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->timestamps();
        });

        // كشف القطع المزيفة
        Schema::create('authenticity_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('check_type'); // image, serial, barcode
            $table->json('submitted_data')->nullable();
            $table->enum('result', ['authentic', 'suspicious', 'fake', 'unknown'])->default('unknown');
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('analysis_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // تحليل سلوك المستخدم
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('event_type'); // view, click, search, add_to_cart, purchase
            $table->string('event_target')->nullable(); // product_id, category_id, etc
            $table->json('event_data')->nullable();
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->integer('time_spent')->nullable(); // seconds
            $table->timestamps();
            
            $table->index(['user_id', 'event_type']);
            $table->index('session_id');
        });

        // محادثات Chatbot المحسّن
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_token')->unique();
            $table->enum('status', ['active', 'closed', 'transferred'])->default('active');
            $table->string('language')->default('ar');
            $table->json('context')->nullable();
            $table->integer('messages_count')->default(0);
            $table->decimal('satisfaction_rating', 3, 2)->nullable();
            $table->boolean('resolved')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // رسائل الـ Chatbot
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ai_chat_sessions')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->text('content');
            $table->json('intent')->nullable();
            $table->json('entities')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('suggestions')->nullable();
            $table->boolean('helpful')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('user_behaviors');
        Schema::dropIfExists('authenticity_checks');
        Schema::dropIfExists('image_searches');
    }
};
