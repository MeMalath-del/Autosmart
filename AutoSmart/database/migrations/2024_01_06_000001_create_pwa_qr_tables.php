<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // أكواد QR للمنتجات
        if (! Schema::hasTable('product_qr_codes')) {
            Schema::create('product_qr_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('code')->unique();
                $table->string('qr_image_path')->nullable();
                $table->integer('scan_count')->default(0);
                $table->timestamp('last_scanned_at')->nullable();
                $table->timestamps();
            });
        }

        // سجل مسح الباركود
        if (! Schema::hasTable('barcode_scans')) {
            Schema::create('barcode_scans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('barcode');
                $table->string('type')->default('product'); // product, qr, ean
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
                $table->boolean('found')->default(false);
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        // إشعارات Push للـ PWA
        if (! Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('endpoint')->unique();
                $table->string('public_key');
                $table->string('auth_token');
                $table->string('content_encoding')->default('aesgcm');
                $table->boolean('is_active')->default(true);
                $table->timestamp('subscribed_at');
                $table->timestamps();
            });
        }

        // إعدادات الإشعارات
        if (! Schema::hasTable('push_notification_settings')) {
            Schema::create('push_notification_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('order_updates')->default(true);
                $table->boolean('promotions')->default(true);
                $table->boolean('price_alerts')->default(true);
                $table->boolean('stock_alerts')->default(true);
                $table->boolean('messages')->default(true);
                $table->timestamps();

                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notification_settings');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('barcode_scans');
        Schema::dropIfExists('product_qr_codes');
    }
};
