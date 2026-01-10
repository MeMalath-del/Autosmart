<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تذاكر الدعم
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->enum('category', ['order', 'payment', 'shipping', 'product', 'account', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('satisfaction_rating')->nullable();
            $table->timestamps();
        });

        // ردود التذاكر
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_from_customer')->default(true);
            $table->timestamps();
        });

        // قاعدة المعرفة
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('category');
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // الأسئلة الشائعة
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('knowledge_base');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
};
