<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // موظفي المتجر
        Schema::create('store_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('position')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('can_manage_products')->default(false);
            $table->boolean('can_manage_orders')->default(false);
            $table->boolean('can_manage_inventory')->default(false);
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_manage_coupons')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['store_id', 'user_id']);
        });

        // سجل عمليات الموظفين
        Schema::create('staff_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // Chatbot المحادثات
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id');
            $table->enum('status', ['active', 'transferred', 'closed'])->default('active');
            $table->foreignId('transferred_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // رسائل Chatbot
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->enum('sender', ['user', 'bot', 'agent']);
            $table->text('message');
            $table->json('intent')->nullable();
            $table->json('entities')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->timestamps();
        });

        // إعدادات Chatbot
        Schema::create('chatbot_intents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('training_phrases');
            $table->json('responses');
            $table->string('action')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // تقارير مجدولة
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('report_type');
            $table->json('parameters')->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->string('email');
            $table->time('send_at')->default('09:00');
            $table->integer('day_of_week')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // التقارير المحفوظة
        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('report_type');
            $table->json('parameters')->nullable();
            $table->json('filters')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_reports');
        Schema::dropIfExists('scheduled_reports');
        Schema::dropIfExists('chatbot_intents');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
        Schema::dropIfExists('staff_activity_logs');
        Schema::dropIfExists('store_staff');
    }
};
