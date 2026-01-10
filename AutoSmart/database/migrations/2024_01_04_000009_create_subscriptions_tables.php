<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // خطط الاشتراك
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly']);
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->json('features')->nullable();
            $table->json('included_products')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // اشتراكات المستخدمين
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->foreignId('user_car_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
        });

        // طلبات الاشتراك (التوصيل الدوري)
        Schema::create('subscription_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('user_subscriptions')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('scheduled_date');
            $table->enum('status', ['scheduled', 'processing', 'delivered', 'skipped', 'failed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // تذكيرات الصيانة
        Schema::create('maintenance_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_car_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // oil_change, tire_rotation, etc
            $table->integer('interval_km')->nullable();
            $table->integer('interval_months')->nullable();
            $table->integer('last_mileage')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_mileage')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('notification_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_reminders');
        Schema::dropIfExists('subscription_deliveries');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
