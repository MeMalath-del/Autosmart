<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إشعارات Push
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('endpoint')->unique();
            $table->string('p256dh_key');
            $table->string('auth_token');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('segment')->nullable(); // all, users, sellers
            $table->integer('sent_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // المنتجات المشاهدة مؤخراً
        Schema::create('recently_viewed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });

        // إعدادات التكامل
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // aramex, smsa, whatsapp, etc
            $table->string('name');
            $table->json('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // تتبع الشحنات من شركات الشحن
        Schema::create('shipping_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('carrier_code');
            $table->string('tracking_number');
            $table->json('tracking_data')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_trackings');
        Schema::dropIfExists('integration_settings');
        Schema::dropIfExists('recently_viewed');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('push_subscriptions');
    }
};
