<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // حملات الإشعارات
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->text('body');
            $table->string('image')->nullable();
            $table->string('action_url')->nullable();
            $table->json('target_segments')->nullable();
            $table->json('target_users')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->timestamps();
        });

        // سجل الإشعارات المرسلة
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('notification_campaigns')->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->string('type')->default('general');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_clicked')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();
        });

        // العروض الموجهة
        Schema::create('targeted_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('offer_type', ['discount', 'free_shipping', 'gift', 'bundle']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->json('target_criteria')->nullable(); // user segments, behaviors
            $table->json('applicable_products')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('uses_count')->default(0);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // عروض المستخدم
        Schema::create('user_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('targeted_offers')->onDelete('cascade');
            $table->boolean('is_claimed')->default(false);
            $table->boolean('is_used')->default(false);
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['user_id', 'offer_id']);
        });

        // تذكيرات الصيانة الذكية
        Schema::create('smart_maintenance_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_car_id')->constrained()->onDelete('cascade');
            $table->string('maintenance_type');
            $table->date('due_date');
            $table->integer('due_mileage')->nullable();
            $table->json('recommended_products')->nullable();
            $table->json('recommended_workshops')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->boolean('notification_sent')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_maintenance_reminders');
        Schema::dropIfExists('user_offers');
        Schema::dropIfExists('targeted_offers');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_campaigns');
    }
};
