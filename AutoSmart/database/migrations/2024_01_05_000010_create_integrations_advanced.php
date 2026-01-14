<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تكاملات WhatsApp
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number');
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->enum('type', ['text', 'template', 'image', 'document']);
            $table->text('content');
            $table->string('template_name')->nullable();
            $table->json('template_params')->nullable();
            $table->string('message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // قوالب WhatsApp
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category'); // order_update, marketing, etc.
            $table->text('content');
            $table->json('variables')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // تكاملات API
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider'); // zapier, erp, accounting
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->json('config')->nullable();
            $table->json('endpoints')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });

        // سجلات الـ API
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->nullable()->constrained('api_integrations')->onDelete('set null');
            $table->string('endpoint');
            $table->string('method');
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->json('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Webhooks
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('secret')->nullable();
            $table->json('events'); // order.created, product.updated, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('failure_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        // سجل Webhooks
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->onDelete('cascade');
            $table->string('event');
            $table->json('payload');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('attempts')->default(1);
            $table->boolean('is_successful')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // تفضيلات الواجهة
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme')->default('light');
            $table->string('primary_color')->nullable();
            $table->string('language')->default('ar');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('whatsapp_notifications')->default(false);
            $table->json('dashboard_widgets')->nullable();
            $table->json('quick_actions')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_integrations');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('whatsapp_messages');
    }
};
